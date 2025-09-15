@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">2段階認証</div>
                
                <div class="card-body">
                    <p class="text-center mb-4">Google Authenticatorアプリで6桁のコードを入力してください。</p>
                    
                    <!-- 2FAコード入力フォーム -->
                    <form method="POST" action="{{ route('admin.two-factor.verify') }}" class="mb-4">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="code" class="form-label">認証コード</label>
                            <input type="text" 
                                   id="code" 
                                   name="code" 
                                   class="form-control @error('code') is-invalid @enderror" 
                                   placeholder="000000" 
                                   maxlength="6" 
                                   required 
                                   autocomplete="one-time-code"
                                   autofocus>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                認証する
                            </button>
                        </div>
                    </form>
                    
                    <!-- リカバリーコード入力フォーム -->
                    <div class="text-center">
                        <button type="button" class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#recoveryForm">
                            認証アプリが利用できない場合
                        </button>
                    </div>
                    
                    <div class="collapse mt-3" id="recoveryForm">
                        <div class="card">
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.two-factor.recovery') }}">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label for="recovery_code" class="form-label">リカバリーコード</label>
                                        <input type="text" 
                                               id="recovery_code" 
                                               name="recovery_code" 
                                               class="form-control @error('recovery_code') is-invalid @enderror" 
                                               placeholder="リカバリーコードを入力" 
                                               required>
                                        @error('recovery_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-outline-primary">
                                            リカバリーコードで認証
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('admin.login') }}" class="btn btn-link">
                            ログイン画面に戻る
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 数字のみ入力可能にする
document.getElementById('code').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});

// 自動フォーカス
document.getElementById('code').focus();
</script>
@endsection
