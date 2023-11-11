<div class="footer">
    @if($isContractLoaded)
    <i class="fa-solid fa-microchip">
    </i>
    <code class="code-blue">{{ substr($mfaContractAddr, 0, 6) }}...{{ substr($mfaContractAddr, -4) }}</code>
    {{ env('THULEEN_SSOMFA_FOOTER_TEXT') }}
    @else
    <span class="font-weight-bold text-danger">
        <i class="fa-solid fa-exclamation-triangle text-danger"></i>
        Contract is not loaded. Check ssomfa-api
    </span>
    @endif
</div>