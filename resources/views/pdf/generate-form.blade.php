<!-- resources/views/pdf/generate-form.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>生成 PDF 文档</h2>
    
    <form action="{{ route('pdf.generate.custom') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="title">文档标题:</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        
        <div class="form-group">
            <label for="content">文档内容:</label>
            <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="filename">文件名:</label>
            <input type="text" class="form-control" id="filename" name="filename" value="document.pdf">
        </div>
        
        <button type="submit" class="btn btn-primary">生成 PDF</button>
    </form>
</div>
@endsection