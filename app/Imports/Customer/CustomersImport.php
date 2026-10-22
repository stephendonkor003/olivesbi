<?php

namespace App\Imports\Customer;

use App\Models\Citizen;
use App\Models\CustomerType;
use App\Models\User;
use App\Support\NewAccountSetup;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;

class CustomersImport implements SkipsEmptyRows, ToModel, WithEvents, WithHeadingRow, WithValidation
{
    use Importable, RegistersEventListeners;

    protected $created_by;

    public function __construct($created_by)
    {
        $this->created_by = $created_by;
    }

    public function model(array $row)
    {
        $randomNumbers = '';
        for ($i = 0; $i < 6; $i++) {
            $randomNumbers .= mt_rand(0, 9);
        }

        // Generate unique account number
        do {
            $accountNumber = 'IPRS'.$randomNumbers;
        } while (Citizen::where('account_number', $accountNumber)->exists());

        $customerType = CustomerType::where('name', 'LIKE', '%'.$row['customer_type'].'%')->first();

        $user = User::create([
            'name' => $row['first_name'].' '.($row['last_name'] ?? ''),
            'email' => $accountNumber.'@iprs.com',
            'phone' => $row['phone'] ?? null,
            'password' => NewAccountSetup::passwordHash(),
            'access_level' => 'customer',
            'status' => 'Active',
            'created_by' => $this->created_by,
        ]);

        return new Citizen([
            'user_id' => $user->id,
            'account_number' => $accountNumber ?? '',
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'] ?? null,
            'gender' => $row['gender'] ?? null,
            'telephone_number' => $row['phone'] ?? null,
            'country_of_citizenship' => $row['country'] ?? null,
            'customer_type' => $customerType->id ?? null,
            'status' => 'Active',
            'created_by' => $this->created_by,
        ]);
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'gender' => ['nullable', 'string', 'in:Male,Female'],
            'phone' => ['required', 'string', 'unique:users,phone'],
            'country' => ['required', 'string', 'in:Ghana,Nigeria'],
            'customer_type' => ['required', 'string'],
        ];
    }

    public static function beforeImport(BeforeImport $event)
    {
        if ($event->getReader()->getActiveSheet()->getHighestRow() <= 1) {
            throw ValidationException::withMessages([
                'file' => 'You cannot upload an empty excel sheet.',
            ]);
        }
    }
}
