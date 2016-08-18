{if $PaypalShowLogo}
    <div class="paypal-sidebar panel">
        <div class="panel--body">
            <a onclick="window.open(this.href, 'olcwhatispaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=400, height=500'); return false;"
               href="https://www.paypal.com/de/cgi-bin/webscr?cmd=xpt/cps/popup/OLCWhatIsPayPal-outside"
               title="{s name='PaypalLogoLinkTitleText'}{/s}"
               target="_blank">
                <img class="paypal-sidebar--logo" src="{link file='frontend/_public/src/img/paypal-logo.png'}"
                     alt="PayPal Logo"/>
            </a>
        </div>
    </div>
{/if}
