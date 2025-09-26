<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patron_registrations', function (Blueprint $table) {
            $table->id();
            $table->integer('LogonBranchID');
            $table->integer('LogonUserID');
            $table->integer('LogonWorkstationID');
            $table->integer('PatronBranchID');
            $table->string('PostalCode')->nullable();
            $table->string('ZipPlusFour')->nullable();
            $table->string('City')->nullable();
            $table->string('State')->nullable();
            $table->string('County')->nullable();
            $table->integer('CountryID')->nullable();
            $table->string('StreetOne')->nullable();
            $table->string('StreetTwo')->nullable();
            $table->string('StreetThree')->nullable();
            $table->string('NameFirst')->nullable();
            $table->string('NameLast')->nullable();
            $table->string('NameMiddle')->nullable();
            $table->string('User1')->nullable();
            $table->string('User2')->nullable();
            $table->string('User3')->nullable();
            $table->string('User4')->nullable();
            $table->string('User5')->nullable();
            $table->string('Gender')->nullable();
            $table->string('Birthdate')->nullable();
            $table->string('PhoneVoice1')->nullable();
            $table->string('PhoneVoice2')->nullable();
            $table->string('PhoneVoice3')->nullable();
            $table->foreignId('Phone1CarrierID')->nullable();
            $table->foreignId('Phone2CarrierID')->nullable();
            $table->foreignId('Phone3CarrierID')->nullable();
            $table->string('EmailAddress')->nullable();
            $table->string('AltEmailAddress')->nullable();
            $table->integer('LanguageID')->nullable();
            $table->string('UserName')->nullable();
            $table->string('Password')->nullable();
            $table->string('Password2')->nullable();
            $table->foreignId('DeliveryOptionID')->nullable();
            $table->tinyText('EnableSMS')->nullable();
            $table->integer('TxtPhoneNumber')->nullable();
            $table->string('Barcode')->nullable();
            $table->integer('EReceiptOptionID')->nullable();
            $table->foreignId('PatronCodeID');
            $table->string('ExpirationDate')->nullable();
            $table->string('AddrCheckDate')->nullable();
            $table->integer('GenderID')->nullable();
            $table->string('LegalNameFirst')->nullable();
            $table->string('LegalNameLast')->nullable();
            $table->string('LegalNameMiddle')->nullable();
            $table->tinyInteger('UseLegalNameOnNotices');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patron_registrations');
    }
};
