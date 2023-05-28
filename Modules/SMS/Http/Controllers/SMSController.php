<?php

namespace Modules\SMS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\SMS\Models\SmsLog;
use Modules\SMS\Models\SmsTemplate;

class SMSController extends Controller
{
    public function send(Request $request)
    {
        $sms_template = SmsTemplate::query()->findOrFail($request->sms_template_id);

        if (!empty($sms_template->variables)) {

            $validator = Validator::make($request->all(), [
                'variables' => [
                    'required',
                    function ($attribute, $value, $fail) use ($sms_template) {
                        $variables = json_decode($sms_template->variables, true, 512, JSON_THROW_ON_ERROR);

                        $missingVariables = [];

                        foreach ($variables as $variable) {

                            $keys = array_keys($value);

                            if (!in_array($variable, $keys, true)) {
                                $missingVariables[] = $variable;
                            }
                        }

                        if (!empty($missingVariables)) {
                            $fail("مقادیر زیر باید وارد شوند: " . implode(', ', $missingVariables));
                        }
                    },
                ],
            ]);

            if ($validator->fails()) {
                return response([
                    'status' => false,
                    'data' => $validator->errors(),
                ], 401);
            }

            $text = $sms_template->body;
            foreach ($request->variables as $key => $value) {
                $text = str_replace("{{$key}}", $value, $text);
            }

        } else {
            $text = $sms_template->body;
        }

        $number = $request->number;
        //TODO: Send SMS of $text to $number

        $sms_log = SmsLog::create([
            'sms_template_id' => $sms_template->id,
            'number' => $number,
            'text' => $text,
        ]);

        return response([
            'status' => true,
            'message' => "Done!",
            "data" => $sms_log
        ], 200);

    }

    public function send_bulk(Request $request)
    {
        foreach ($request->all() as $object) {

            $sms_template = SmsTemplate::query()->findOrFail($object['sms_template_id']);

            if (!empty($sms_template->variables)) {

                $validator = Validator::make($object, [
                    'variables' => [
                        'required',
                        function ($attribute, $value, $fail) use ($sms_template) {
                            $variables = json_decode($sms_template->variables, true, 512, JSON_THROW_ON_ERROR);

                            $missingVariables = [];

                            foreach ($variables as $variable) {

                                $keys = array_keys($value);

                                if (!in_array($variable, $keys, true)) {
                                    $missingVariables[] = $variable;
                                }
                            }

                            if (!empty($missingVariables)) {
                                $fail("مقادیر زیر باید وارد شوند: " . implode(', ', $missingVariables));
                            }
                        },
                    ],
                ]);

                if ($validator->fails()) {
                    return response([
                        'status' => false,
                        'data' => $validator->errors(),
                    ], 401);
                }

                $text = $sms_template->body;
                foreach ($object['variables'] as $key => $value) {
                    $text = str_replace("{{$key}}", $value, $text);
                }

            } else {
                $text = $sms_template->body;
            }

            $number = $object['number'];
            //TODO: Send SMS of $text to $number

            $sms_log = SmsLog::create([
                'sms_template_id' => $sms_template->id,
                'number' => $number,
                'text' => $text,
            ]);
        }

        return response([
            'status' => true,
            'message' => "Done!",
        ], 200);

    }

    public function logs()
    {
        $sms_logs = SmsLog::all();

        return response([
            'status' => true,
            'data' => $sms_logs,
        ], 200);
    }
}
