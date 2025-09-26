<?php

namespace Blashbrook\PAPIClient\Models;

use Blashbrook\PAPIClient\Models\{DeliveryOption,PatronCode};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatronRegistration extends Model
{

    protected $fillable = [
        'LogonBranchID',
        'LogonUserID',
        'LogonWorkstationID',
        'PatronBranchID',
        'PostalCode',
        'ZipPlusFour',
        'City',
        'State',
        'County',
        'CountryID',
        'StreetOne',
        'StreetTwo',
        'StreetThree',
        'NameFirst',
        'NameLast',
        'NameMiddle',
        'User1',
        'User2',
        'User3',
        'User4',
        'User5',
        'Gender',
        'Birthdate',
        'PhoneVoice1',
        'PhoneVoice2',
        'PhoneVoice3',
        'Phone1CarrierID',
        'Phone2CarrierID',
        'Phone3CarrierID',
        'EmailAddress',
        'AltEmailAddress',
        'LanguageID',
        'UserName',
        'Password',
        'Password2',
        'DeliveryOptionID',
        'EnableSMS',
        'TxtPhoneNumber',
        'Barcode',
        'EReceiptOptionID',
        'PatronCodeID',
        'ExpirationDate',
        'AddrCheckDate',
        'GenderID',
        'LegalNameFirst',
        'LegalNameLast',
        'LegalNameMiddle',
        'UseLegalNameOnNotices',
    ];

    public function deliveryOptionID(): BelongsTo
    {
        return $this->belongsTo(DeliveryOption::class, 'DeliveryOptionID');
    }

    public function patronCodeID(): BelongsTo
    {
        return $this->belongsTo(PatronCode::class, 'PatronCodeID');
    }
}
