@if ($errors->any())
<script>
    toastr.error('{{ $errors->first() }}')
</script>
@endif

@if (session()->has('success'))
<script>
    toastr.success("{{ session()->get('success') }}")
</script>
@endif