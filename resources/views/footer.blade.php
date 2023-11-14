<div class="footer">
    @if($isContractLoaded)
    <i class="fa-solid fa-microchip">
    </i>
    <code class="code-blue">{{ substr($mfaContractAddr, 0, 6) }}...{{ substr($mfaContractAddr, -4) }}</code> loaded.
    {{ env('THULEEN_SSOMFA_FOOTER_TEXT') }}
    @else
    <i class="fa-solid fa-exclamation-triangle text-warning">
    </i>
    WARNING! CONTRACT IS NOT LOADED!
    @endif
</div>