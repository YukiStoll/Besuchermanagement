<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Summernote;
use Illuminate\Support\Facades\Log;

class emailTemplateController extends UNOController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $summernote = Summernote::all()->find($request->get('id'));
        if($request['language'] == "german")
        {
            return $this->test(view('EMailTemplate')->with(
                [
                    'id' => $request->get('id'),
                    'detail' => $summernote['content_de'],
                    'language' => $request->get('language'),
                ]
            ));
        }
        else
        {
            return $this->test(view('EMailTemplate')->with(
                [
                    'id' => $request->get('id'),
                    'detail' => $summernote['content_en'],
                    'language' => $request->get('language'),
                ]
            ));
        }
    }

    public function post(Request $request)
    {
        $request->validate(
            [
                'content' => 'required',
            ]
        );
        if($request['language'] == "german")
        {
            $request['content_de'] = $request->input('content');
        }
        else
        {
            $request['content_en'] = $request->input('content');
        }
        $summernote = summernote::all()->find($request['id']);
        if(!empty($summernote))
        {
            $summernote->update($request->all());
            if($request['language'] == "german")
            {
                return $this->test(view('EMailTemplate')->with(
                    [
                        'id' => $request->get('id'),
                        'detail' => $summernote['content_de'],
                        'language' => $request->get('language'),
                        'saved' => true,
                    ]
                ));
            }
            else
            {
                return $this->test(view('EMailTemplate')->with(
                    [
                        'id' => $request->get('id'),
                        'detail' => $summernote['content_en'],
                        'language' => $request->get('language'),
                        'saved' => true,
                    ]
                ));
            }
        }
        else
        {
            if($request['language'] == "german")
            {
                return $this->test(view('EMailTemplate')->with(
                    [
                        'id' => $request->get('id'),
                        'detail' => $summernote['content_de'],
                        'language' => $request->get('language'),
                        'saved' => false,
                    ]
                ));
            }
            else
            {
                return $this->test(view('EMailTemplate')->with(
                    [
                        'id' => $request->get('id'),
                        'detail' => $summernote['content_en'],
                        'language' => $request->get('language'),
                        'saved' => false,
                    ]
                ));
            }
        }
    }
}
