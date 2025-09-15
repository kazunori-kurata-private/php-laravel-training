@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">2段階認証設定</div>
                
                <div class="card-body">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        2段階認証が有効になっています。
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>設定情報</h5>
                            <p><strong>有効化日時:</strong> {{ Auth::user()->two_factor_confirmed_at->format('Y年m月d日 H:i') }}</p>
                            <p><strong>状態:</strong> <span class="text-success">有効</span></p>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>リカバリーコード</h5>
                            <p>認証アプリが利用できない場合に使用できるリカバリーコードが生成されています。</p>
                            <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#recoveryCodesModal">
                                リカバリーコードを表示
                            </button>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                            ダッシュボードに戻る
                        </a>
                        
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#disableModal">
                            2FAを無効化
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- リカバリーコード表示モーダル -->
<div class="modal fade" id="recoveryCodesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">リカバリーコード</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <strong>重要:</strong> これらのコードは安全な場所に保管してください。各コードは一度しか使用できません。
                </div>
                <div class="row">
                    @php
                        $recoveryCodes = json_decode(Auth::user()->two_factor_recovery_codes, true) ?? [];
                    @endphp
                    @foreach($recoveryCodes as $code)
                        <div class="col-md-6 mb-2">
                            <code>{{ $code }}</code>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
            </div>
        </div>
    </div>
</div>

<!-- 2FA無効化確認モーダル -->
<div class="modal fade" id="disableModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">2FA無効化の確認</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.two-factor-auth.disable') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <strong>警告:</strong> 2段階認証を無効化すると、アカウントのセキュリティが低下します。
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">現在のパスワード</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-danger">2FAを無効化</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
