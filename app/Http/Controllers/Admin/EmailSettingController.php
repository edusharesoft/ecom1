<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailSettingController extends Controller
{
    public function index()
    {
        return view('backEnd.email_setting.index', [
            'mail' => [
                'MAIL_MAILER' => env('MAIL_MAILER'),
                'MAIL_HOST' => env('MAIL_HOST'),
                'MAIL_PORT' => env('MAIL_PORT'),
                'MAIL_USERNAME' => env('MAIL_USERNAME'),
                'MAIL_PASSWORD' => env('MAIL_PASSWORD'),
                'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION'),
                'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS'),
                'MAIL_FROM_NAME' => env('MAIL_FROM_NAME'),
            ]
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'MAIL_MAILER' => 'required|string',
            'MAIL_HOST' => 'required|string',
            'MAIL_PORT' => 'required|string',
            'MAIL_USERNAME' => 'required|string',
            'MAIL_PASSWORD' => 'required|string',
            'MAIL_ENCRYPTION' => 'required|string',
            'MAIL_FROM_ADDRESS' => 'required|email',
            'MAIL_FROM_NAME' => 'required|string',
        ]);

        $this->setEnvironmentValue($data);

        return redirect()->back()->with('success', '✅ Email settings updated successfully!');
    }

    private function setEnvironmentValue(array $values)
    {
        $envFile = base_path('.env');
        $content = file_get_contents($envFile);

        foreach ($values as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $value = '"' . trim($value) . '"';
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envFile, $content);
    }
}
