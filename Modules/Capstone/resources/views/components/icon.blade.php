@props(['name', 'size' => 16, 'weight' => 'regular'])
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Log;

    $map = [
        'LayoutDashboard' => 'squares-four', 'SquaresFour' => 'squares-four',
        'Calendar' => 'calendar-blank', 'CalendarIcon' => 'calendar-blank', 'CalendarBlank' => 'calendar-blank',
        'CalendarPlus' => 'calendar-plus', 'CalendarCheck' => 'calendar-check',
        'ChevronLeft' => 'caret-left', 'CaretLeft' => 'caret-left',
        'ChevronRight' => 'caret-right', 'CaretRight' => 'caret-right',
        'ChevronDown' => 'caret-down', 'CaretDown' => 'caret-down',
        'ChevronUp' => 'caret-up',
        'Search' => 'magnifying-glass', 'MagnifyingGlass' => 'magnifying-glass',
        'Bell' => 'bell', 'Menu' => 'list', 'List' => 'list', 'X' => 'x',
        'User' => 'user', 'UserRound' => 'user-circle', 'UserCircle' => 'user-circle',
        'LogOut' => 'sign-out', 'SignOut' => 'sign-out',
        'Settings' => 'gear', 'Gear' => 'gear', 'House' => 'house',
        'Users' => 'users', 'GraduationCap' => 'graduation-cap',
        'FileText' => 'file-text', 'FileCheck' => 'checks', 'File' => 'file',
        'BookOpen' => 'book-open',
        'Gavel' => 'gavel', 'Presentation' => 'presentation-chart', 'PresentationChart' => 'presentation-chart',
        'Star' => 'star', 'ClipboardCheck' => 'clipboard-text', 'ClipboardText' => 'clipboard-text',
        'ListChecks' => 'list-checks', 'BarChart3' => 'chart-bar', 'ChartBar' => 'chart-bar',
        'TrendingUp' => 'trend-up', 'TrendUp' => 'trend-up',
        'ScrollText' => 'scroll', 'Scroll' => 'scroll',
        'MapPin' => 'map-pin', 'UserPlus' => 'user-plus',
        'ShieldCheck' => 'shield-check', 'Library' => 'library',
        'Check' => 'check', 'ArrowUpDown' => 'arrows-down-up', 'ArrowsDownUp' => 'arrows-down-up',
        'ArrowRight' => 'arrow-right', 'ArrowLeft' => 'arrow-left',
        'Info' => 'info', 'TriangleAlert' => 'warning-circle', 'WarningCircle' => 'warning-circle',
        'CircleAlert' => 'warning-circle', 'CircleX' => 'x-circle', 'XCircle' => 'x-circle',
        'CircleCheck' => 'check-circle', 'CheckCircle' => 'check-circle',
        'Circle' => 'circle', 'Clock' => 'clock', 'Plus' => 'plus', 'Minus' => 'minus',
        'Lock' => 'lock', 'Send' => 'paper-plane-tilt', 'PaperPlaneTilt' => 'paper-plane-tilt',
        'Edit' => 'pencil-simple', 'PencilSimple' => 'pencil-simple',
        'PenLine' => 'pencil-line', 'PencilLine' => 'pencil-line',
        'Trash2' => 'trash', 'Trash' => 'trash',
        'Ellipsis' => 'dots-three', 'DotsThree' => 'dots-three',
        'Eye' => 'eye', 'SquarePen' => 'note-pencil', 'NotePencil' => 'note-pencil',
        'RefreshCw' => 'arrow-clockwise', 'ArrowClockwise' => 'arrow-clockwise',
        'Funnel' => 'funnel', 'Mail' => 'envelope', 'Envelope' => 'envelope',
        'Upload' => 'upload-simple', 'UploadSimple' => 'upload-simple',
        'Download' => 'download-simple', 'DownloadSimple' => 'download-simple',
        'Square' => 'square', 'Lightbulb' => 'lightbulb',
        'ThumbsUp' => 'thumbs-up', 'ThumbsDown' => 'thumbs-down',
    ];
    $kebab = $map[$name] ?? Str::kebab($name);
    $allowed = array_unique(array_values($map));
    if (! in_array($kebab, $allowed, true)) {
        Log::warning('capstone.icon.unknown', ['name' => $name]);
    }
    $weightClass = $weight === 'regular' ? 'ph' : 'ph-'.$weight;
@endphp
<i class="{{ $weightClass }} ph-{{ $kebab }}" style="font-size:{{ is_numeric($size) ? $size.'px' : $size }}" aria-hidden="true" {{ $attributes }}></i>
