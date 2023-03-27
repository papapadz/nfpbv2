<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use App\Position;
use App\Salary;
use App\Employment;
use App\Education;
use App\Family;
use App\License;
use App\FileHandler;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use DB;

class EmployeeController extends Controller
{
    public function store(Request $request) {
        
       //dd(json_decode($request->education)[0]->year);
        $employee_id = $this->generateEmployeeID();
        $request->request->add(['employee_id' => $employee_id]);

        $position = $this->findPosition($request->currentPosition);

        /** Create salary */
        $salary = Salary::create([
            'position_id' => $position->id,
            'amount' => $request->currentSalary,
            'date_effective' => $request->currentDateEmployed,
            'monthly' => $request->currentSalary > 5000 ? true : false
        ]);

        /** Create/Update Employee */
        $employee = Employee::updateOrCreate([
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'birthdate' => $request->dateOfBirth
        ],[
            'middle_name' => $request->middleName,
            'employee_id' => $employee_id,
            'gender' => $request->sex,
            'civil_Stat' => $request->civilStatus,
            'citizenship' => $request->citizenship,
            'address' => $request->address,
            'height' => $request->height,
            'weight' => $request->weight,
            'bloodType' => $request->bloodType,
            //'date_hired' => $request->currentDateEmployed,
            'email' => $request->emailAddress,
            //'position' => $position->id
            'civil_stat' => $request->civilStatus,
            'img' => $this->uploadFile($request->file('picture'))
        ]);

        /** Create Education */
        if($request->has('education')) {
            foreach(json_decode($request->education) as $educ) {
                if($educ->school) {
                    $myRequest = new Request();
                    $myRequest->replace([
                        'employee_id' => $employee_id,
                        'level' => $educ->level,
                        'school' => $educ->school,
                        'year' => $educ->year
                    ]);
                    $this->addEducation($myRequest);
                }
            }
        }

        /** Create Current Employment */
        $employment = Employment::create([
            'employee_id' => $employee_id,
            'salary_id' => $salary->id,
            'status' => $salary->monthly ? 'Regulary' : 'COSW',
            'date_hired' => $salary->date_effective,
            'company' => 'Northflash Power and Builds, Inc.',
            'is_active' => true
        ]);

        /** Create Previous Employment */
        if($request->has('workExperience')) {
            foreach(json_decode($request->workExperience) as $exp) {
                if($exp->companyName) {
                    
                    $d = Carbon::now()->startOfYear();
                    $d->year = $exp->year;
                    
                    $expSalary = Salary::create([
                        'position_id' => $this->findPosition($exp->position)->id,
                        'amount' => $exp->salary,
                        'date_effective' => $d->toDateString(),
                        'monthly' => $exp->salary > 5000 ? true : false
                    ]);

                    $myRequest = new Request();
                    $myRequest->replace([
                        'employee_id' => $employee_id,
                        'salary_id' => $expSalary->id,
                        'status' => $expSalary ? 'Regular' : 'COSW',
                        'date_hired' => $expSalary->date_effective,
                        'company' => $exp->companyName
                    ]);
                    $workExp = $this->addWorkExp($myRequest);
                }
            }
        }

        /** Add Family Background */
        if($request->has('spouseName')) {
            if($request->spouseName) {
                Family::updateOrCreate([
                    'employee_id' => $employee_id,
                    'relationship' => 'Spouse',
                ],[
                    'name' => $request->spouseName,
                    'phone' => $request->spousePhone,
                    'occupation' => $request->spouseOccupation
                ]);
            }
        }

            Family::updateOrCreate([
                'employee_id' => $employee_id,
                'relationship' => 'Father',
            ],[
                'name' => $request->fatherName,
            ]);

            Family::updateOrCreate([
                'employee_id' => $employee_id,
                'relationship' => 'Mother',
            ],[
                'name' => $request->motherName,
            ]);

            if($request->has('eligibility') && $request->eligibility)
                License::updateOrCreate([
                    'employee_id' => $employee_id,
                    'license_type_id' => 5
                ],[
                    'license_no' => $request->eligibility,
                ]);

            if($request->has('tin') && $request->tin)
                License::updateOrCreate([
                    'employee_id' => $employee_id,
                    'license_type_id' => 1
                ],[
                    'license_no' => $request->tin,
                ]);

            if($request->has('pagibigIdNo') && $request->pagibigIdNo)
                License::updateOrCreate([
                    'employee_id' => $employee_id,
                    'license_type_id' => 4
                ],[
                    'license_no' => $request->pagibigIdNo
                ]);
            
            if($request->has('philHealthNo') && $request->philHealthNo)
                License::updateOrCreate([
                    'employee_id' => $employee_id,
                    'license_type_id' => 3
                ],[
                    'license_no' => $request->philHealthNo
                ]);

            if($request->has('sssNo') && $request->sssNo)
                License::updateOrCreate([
                    'employee_id' => $employee_id,
                    'license_type_id' => 2
                ],[
                    'license_no' => $request->sssNo
                ]);
        
        return array(
            'code' => 'OK',
            'status' => $employee->wasRecentlyCreated ? 'UPDATED' : 'NEW',
            'employee_id' => $employee_id
        );
    }

    public function findPosition($title) {
        $position = Position::select('id')->where('title','like','%'.$title)->first();

        if(!$position) {
            $position = Position::create([
                'title' => $title
            ]);
        }
        return $position;
    }

    public function addEducation(Request $request) {
        $education = Education::updateOrCreate([
            'employee_id' => $request->employee_id,
            'level' => $request->level,
            'school' => $request->school,
            'year' => $request->year
        ]);

        return $education;
    }

    public function addWorkExp(Request $request) {
        $employment = Employment::updateOrCreate([
            'employee_id' => $request->employee_id,
            'company' => $request->company
        ],[
            'salary_id' => $request->salary_id,
            'status' => $request->status,
            'date_hired' => $request->date_hired,
        ]);

        return $employment;
    }

    public function generateEmployeeID() {
        
        $id = null;
        $isOkay = false;
        do {
            $id = Carbon::now()->format('y').'-'.str_pad(rand(1,9999), 4, '0', STR_PAD_LEFT);
            $employee = Employee::find($id);
            if($employee)
                $isOkay = true;
        } while($isOkay);

        return $id;
    }

    public function uploadFile($image) {
        $filename = time() . '.' . $image->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('uploads', $image, $filename);
        
        $fileObj = FileHandler::create([
            'file_type' => $image->getClientOriginalExtension(),
            'url' => asset('storage/uploads/'.$filename)
        ]);
        return $fileObj->id;
    }

    public function get() {
        return Employee::with([
            'education','employment','family','licenses'
        ])->get();
    }
}
