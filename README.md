![alt text](https://github.com/abolfazlrastegar/laravel-sms/blob/main/laravel-sms.jpg?raw=true)

<p align="center">
<a href="https://packagist.org/packages/abolfazlrastegar/laravel-sms"><img src="https://img.shields.io/packagist/dm/abolfazlrastegar/laravel-sms" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/abolfazlrastegar/laravel-sms"><img src="https://img.shields.io/packagist/v/abolfazlrastegar/laravel-sms" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/abolfazlrastegar/laravel-sms"><img src="https://img.shields.io/github/license/abolfazlrastegar/laravel-payments" alt="License"></a>
</p>

### Package Larave-sms
With this package, you can use the capabilities of the SMS system sms.ir and kavenegar

### Install package laravel-sms
```bash
  composer require abolfazlrastegar/laravel-sms
```

### Publish config 
```bash
  php artisan vendor:publish --tag="config"
```

### Docs drivers
<a href="https://apidocs.sms.ir/bulksmsv2.html">sms.ir</a>

<a href="https://kavenegar.com/rest.html#call-maketts">kavenegar</a>

[//]: # (<a href="https://www.melipayamak.com/api/">milepayamak</a>)

### Use method `sendVerifyCode`
```bash
  // this model SMS system kavenegar
  Sms::make('kavenegar')
     ->mobile('09105805770')
     ->template('454545')
     ->params(['token' => 122254])
     ->sendVerifyCode();
```
### or 
```bash
  Sms::make()
     ->defaultSms()
     ->mobile('09105805770')
     ->template('454545')
     ->params(['token' => '122254', 'token2' => '54875', 'token3' => '54875'])
     ->sendVerifyCode();
```

### Use method `sendMessages`
```bash
  Sms::make('kavenegar')
     ->mobile(['09105805772', '09105805772', '09105805772'])
     ->message('set message for send')
     ->params([
        'date' => 'اختیاری',
        'sender' => 'اختیاری',
        'type' => 'اختیاری',
        'localid' => 'اختیاری'
     ]) 
     ->sendMessages();
```
### or
```bash
  Sms::make()
     ->defaultSms()
     ->mobile(['09105805772', '09105805772', '09105805772'])
     ->message('set message for send')
     ->params([
        'date' => 'اختیاری',
        'sender' => 'اختیاری',
        'type' => 'اختیاری',
        'localid' => 'اختیاری'
     ])
     ->sendMessages();
```
### Use method `sendMessageGroup`
```bash
  Sms::make('kavenegar')
     ->mobile(['09105805772', '09105805772', '09105805772'])
     ->message(['set message for send1', 'set message for send2', 'set message for send3'])
     ->params([
         'sender' => ['5455557', '987565423', '6322154'],
         'date' => 'اختیاری',
         'type' => 'اختیاری',
         'localmessageids' => 'اختیاری',
     ])
     ->sendMessageGroup();
```
### or
```bash
  Sms::make()
     ->defaultSms()
     ->mobile(['09105805772', '09105805772', '09105805772'])
     ->message(['set message for send1', 'set message for send2', 'set message for send3'])
     ->params([
         'sender' => ['5455557', '987565423', '6322154'],
         'date' => 'اختیاری',
         'type' => 'اختیاری',
         'localmessageids' => 'اختیاری',
     ])
     ->sendMessageGroup();
```
#
### Use method `voiceCall`
this metode SMS system ['kavenegar'] support
```bash
  Sms::make('kavenegar')
    ->message('set message for voice call')
    ->mobile(['09105805772', '09105805772', '09105805772'])
    ->voiceCall();
```
### or
```bash
  Sms::make()
    ->defaultSms()
    ->message('set message for voice call')
    ->mobile(['09105805772', '09105805772', '09105805772'])
    ->voiceCall();
```
###
### Function Parameter
| Driver      | Method      | Parameter                                                                                                                                                                                                           | Support |
|-------------|-------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|---------|
| kavenegar   | params()    | ['token' => 122254] // one parameter <br/>['token' => 122254, 'token2' => 54875, 'token3' => 54875] // multi parameter                                                                                              | Yes     |
 | Sms.ir      | params()    | ['name' => 'verify', 'value' => 45666] // send message code verify login <br/> [<br/>['name' => 'name_product', 'value' => 45666]<br/>['name' => 'price', 'value' => 5000]<br/>] // send message Factor buy product | Yes     |
 | Kavenegar   | mobile()    | '09105805770' // use for one user<br/> ['09105805770', '09105805770', '09105805770'] // Use for users <br/>                                                                                                         | Yes     |
| Sms.ir      | mobile()    | '09105805770' // use for one user<br/> ['09105805770', '09105805770', '09105805770'] // Use for users <br/>                                                                                                         | Yes     |
| Kavenegar   | voiceCall() |                                                                                                                                                                                                                     | Yes     |
| Sms.ir      | voiceCall() |                                                                                                                                                                                                                     | No      |
| Kavenegar   | message() |                                                                                                                                                                                                                     | Yes     |
| Sms.ir      | message() |                                                                                                                                                                                                                     | Yes      |      
