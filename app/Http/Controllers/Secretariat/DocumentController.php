<?php

namespace App\Http\Controllers\Secretariat;

use App\Models\Role;
use App\Models\User;
use App\Models\Semester;
use App\Models\ImportItem;
use App\Console\Commands;
use App\Utils\Printer;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        Gate::authorize('document.any');

        return view('secretariat.document.index');
    }

    /** Register statement */

    public function printRegisterStatement()
    {
        Gate::authorize('document.register-statement');

        $result = $this->generateRegisterStatement();
        return $this->printDocument($result, __('document.register-statement'));
    }

    public function downloadRegisterStatement()
    {
        Gate::authorize('document.register-statement');

        $result = $this->generateRegisterStatement();
        return $this->downloadDocument($result);
    }

    /** Import license */

    public function printImport()
    {
        Gate::authorize('document.import-license');

        $result = $this->generateImport();
        return $this->printDocument($result, __('document.import'));
    }

    public function downloadImport()
    {
        Gate::authorize('document.import-license');

        $result = $this->generateImport();
        return $this->downloadDocument($result);
    }

    public function showImport()
    {
        Gate::authorize('document.import-license');

        return view('secretariat.document.import', ['items' => user()->importItems]);
    }

    public function addImport(Request $request)
    {
        Gate::authorize('document.import-license');

        ImportItem::create([
            'user_id' => user()->id,
            'name' => $request->item,
            'serial_number' => $request->serial_number ?? null
        ]);
        return redirect()->back()->with('message', __('general.successful_modification'));
    }

    public function removeImport(Request $request)
    {
        Gate::authorize('document.import-license');

        ImportItem::findOrFail($request->id)->delete();
        return redirect()->back()->with('message', __('general.successful_modification'));
    }

    /** Status certificate */

    public function downloadStatusCertificate()
    {
        Gate::authorize('document.status-certificate');

        $result = $this->generateStatusCertificate(user());
        return $this->downloadDocument($result);
    }

    public function showStatusCertificate($id)
    {
        Gate::authorize('document.status-certificate.viewAny');

        $user = User::findOrFail($id);
        $result = $this->generateStatusCertificate($user);
        return $this->downloadDocument($result);
    }

    public function requestStatusCertificate()
    {
        Gate::authorize('document.status-certificate');

        $url = route('documents.status-cert.show', ['id' => user()->id]);
        $secretaries = User::withRole(Role::SECRETARY)->get();
        foreach ($secretaries as $recipient) {
            Mail::to($recipient)->queue(new \App\Mail\StateCertificateRequest($recipient->name, user()->name, $url));
        }

        return redirect()->back()->with('message', "Sikeres igénylés. Az igazolást hamarosan megtalálhatod a titkárságon.");
    }

    /** Private helper functions */


    private function downloadDocument($result)
    {
        if (!$result['success']) {
            return $result['redirect'];
        }
        $document = $result['pdf'];
        return response()->download($document);
    }

    private function printDocument($result, $filename)
    {
        if (!$result['success']) {
            return $result['redirect'];
        }
        $document = $result['pdf'];
        $printer = new Printer($filename, $document, /* $use_free_pages */ true);
        return $printer->print();
    }

    // Returns the .tex file in debug mode
    private function generatePDF($path, $data)
    {
        $renderedLatex = view($path)->with($data)->render();

        $filename =  md5(rand(0, 100000) . date('c'));
        Storage::disk('latex')->put($filename . '.tex', $renderedLatex);

        $outputDir = Storage::disk('latex')->path('/');

        $pathTex = Storage::disk('latex')->path($filename . ".tex");
        $pathPdf = Storage::disk('latex')->path($filename . ".pdf");

        // TODO: figure out result
        Commands::latexToPdf($pathTex, $outputDir);

        if (config('app.debug') && !config('commands.run_in_debug')) {
            return $pathTex;
        } else {
            return $pathPdf;
        }
    }

    private function generateRegisterStatement()
    {
        $user = user();

        if (!$user->hasPersonalInformation()) {
            return [
                'success' => false,
                'redirect' => back()->withInput()->with('error', __('document.missing_personal_info'))
            ];
        }
        $info = $user->personalInformation;

        $pdf = $this->generatePDF(
            'latex.register-statement',
            [ 'name' => $user->name,
              'address' => $info->zip_code . ' ' . $info->getAddress(),
              'phone' => $info->phone_number,
              'email' => $user->email,
              'place_and_of_birth' => $info->getPlaceAndDateOfBirth(),
              'mothers_name' => $info->mothers_name,
              'date' => date("Y.m.d"),
        ]
        );
        return ['success' => true, 'pdf' => $pdf];
    }

    private function generateImport()
    {
        $user = user();
        $items = $user->importItems;

        if ($items->isEmpty()) {
            return [
                'success' => false,
                'redirect' => back()->withInput()->with('error', "Még nem adtad meg a tárgyakat, amiket behoznál.")
            ];
        }

        $pdf = $this->generatePDF(
            'latex.import',
            [ 'name' => $user->name,
              'items' => $items,
              'date' => date("Y.m.d"),
        ]
        );
        return ['success' => true, 'pdf' => $pdf];
    }

    private function generateStatusCertificate($user)
    {
        if (!$user->hasPersonalInformation()) {
            return [
                'success' => false,
                'redirect' => back()->withInput()->with('error', "A személyes adataid hiányoznak a dokumentum kitöltéséhez. Kérj segítséget egy rendszergazdától.")
            ];
        }

        if (!$user->hasEducationalInformation()) {
            return [
                'success' => false,
                'redirect' => back()->withInput()->with('error', "A tanulmányi adataid hiányoznak a dokumentum kitöltéséhez. Kérj segítséget egy rendszergazdától")
            ];
        }
        $personalInfo = $user->personalInformation;
        $educationalInfo = $user->educationalInformation;

        $pdf = $this->generatePDF(
            'latex.status-cert',
            [ 'name' => $user->name,
              'address' => $user->zip_code . ' ' . $personalInfo->getAddress(),
              'place_and_date_of_birth' => $personalInfo->getPlaceAndDateOfBirth(),
              'mothers_name' => $personalInfo->mothers_name,
              'neptun' => $educationalInfo->neptun,
              'from' => $educationalInfo->year_of_acceptance,
              'until' => Semester::current()->getEndDate()->format('Y.m.d.'), // TODO: check active semesters
              // TODO: add status
        ]
        );

        return ['success' => true, 'pdf' => $pdf];
    }
}
