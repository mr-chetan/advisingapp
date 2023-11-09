@props(['url', 'emailTemplate' => null])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if ($emailTemplate?->hasMedia('logo'))
                <img src="{{ $emailTemplate?->getFirstTemporaryUrl(now()->addDays(6), 'logo') }}" class="logo"
                     alt="Logo">
            @else
                {{ $slot }}
            @endif
        </a>
    </td>
</tr>
