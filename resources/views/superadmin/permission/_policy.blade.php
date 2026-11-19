<script>
    window.superadminRoleModules = @json(\App\Services\PermissionAssigner::roleModuleMapping());
</script>
<script src="{{ asset('js/superadmin-permissions.js') }}?v={{ filemtime(public_path('js/superadmin-permissions.js')) }}"></script>
