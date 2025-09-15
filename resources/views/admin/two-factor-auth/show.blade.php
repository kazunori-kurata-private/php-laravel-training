@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">2段階認証の設定</div>
                
                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    @if(session('recovery_codes'))
                        <div class="alert alert-info">
                            <h5>リカバリーコード</h5>
                            <p>以下のリカバリーコードを安全な場所に保管してください。各コードは一度しか使用できません。</p>
                            <div class="row">
                                @foreach(session('recovery_codes') as $code)
                                    <div class="col-md-6 mb-2">
                                        <code>{{ $code }}</code>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p>Google Authenticatorアプリで以下のQRコードをスキャンしてください：</p>
                        
                        <div class="text-center mb-4">
                            {!! $qrCode['qr_code'] !!}
                        </div>
                        
                        <div class="alert alert-info">
                            <strong>手動入力用の秘密鍵:</strong><br>
                            <code>{{ $qrCode['secret'] }}</code>
                        </div>
                        
                        <form method="POST" action="{{ route('admin.two-factor-auth.enable') }}">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="code" class="form-label">認証コード</label>
                                <input type="text" 
                                       id="code" 
                                       name="code" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       placeholder="6桁のコードを入力" 
                                       maxlength="6" 
                                       required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    2FAを有効化
                                </button>
                            </div>
                        </form>
                    @endif
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                            ダッシュボードに戻る
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(!session('recovery_codes'))
<script>
// 数字のみ入力可能にする
document.getElementById('code').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});

// 自動フォーカス
document.getElementById('code').focus();
</script>
@endif
@endsection