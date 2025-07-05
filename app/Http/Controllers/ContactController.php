<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactAdminMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index() {
        return view('contact.index');
    }

    public function complete() {
        return view('contact.complete');
    }

    // バリデーションチェック
    public function sendMail(ContactRequest $request) {
        $validated = $request->validated();

        // これ以降の行は入力エラーがなかった場合のみ実行
        // 登録処理(実際はメール送信などを行う)
        Mail::to('admin@example.com')->send(new ContactAdminMail($validated));
        return to_route('contact.complete');
    }
}
