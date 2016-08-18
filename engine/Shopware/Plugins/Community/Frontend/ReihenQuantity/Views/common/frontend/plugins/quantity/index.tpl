{block name='frontend_detail_buy_quantity'}
{if $sArticle.laststock && !$sArticle.sVariants && $sArticle.instock < $sArticle.maxpurchase}
	{assign var=maxQuantity value=$sArticle.instock+1}
{else}
	{assign var=maxQuantity value=$sArticle.maxpurchase}
{/if}
<label for="sQuantity">{s name="DetailBuyLabelQuantity" namespace="frontend/detail/buy"}{/s}:</label>
{/block}
				

{block name="frontend_detail_buy" append}
    <script type="text/javascript">
    jQuery(document).ready(function($) {
    	$("#detailCartButton label:first").after('<div id="quantityNum"><input id="sQuantity" name="sQuantity" type="text" value="{$sArticle.minpurchase}"><div id="quantityWrapper"><a class="button plus">+</a><a class="button minus">-</a></div><div id="packunit">{$sArticle.packunit}</div><div class="clear">&nbsp;</div></div>');
    	
		$("#quantityNum .button").click(function() {
		    var $button = $(this);
		    var oldValue = parseFloat($("#quantityNum #sQuantity").val());
		    if ($button.text() == "+") {
		    	var newVal = oldValue + {$sArticle.purchasesteps};
		    	if (newVal <= {$maxQuantity}) {
		    		$("#quantityNum #sQuantity").val(newVal);
				}
				
			} else {
				var newVal = oldValue - {$sArticle.purchasesteps};
				if (newVal >= {$sArticle.minpurchase}) {
				    $("#quantityNum #sQuantity").val(newVal);
			  	}
			}
			
		});
		
		$("#quantityNum #sQuantity").ForceNumbersOnly();
	});
	
	
// Nur Zahlen zulassen
jQuery.fn.ForceNumbersOnly =
function(){
    return this.each(function(){
        $(this).keydown(function(e){
            var key = e.charCode || e.keyCode || 0;
            return (
                key == 8 || 
                key == 9 ||
                key == 46 ||
                (key >= 37 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        });
    });
};	

    </script>
{/block}

{block name="frontend_index_header_css_screen" append}
<style type="text/css">
    #quantityNum{
    	margin-bottom: 10px;
    }
    #quantityNum #sQuantity{
    	margin:0;
    	font-size: 17px;
    	float:left;
    	padding: 7px 5px;
    	text-align:center;
    	width:{$ReihenQuantityConfig->inputWidth}px !important;
    }
    #quantityNum #quantityWrapper{
    	margin-left: 5px; 
    	height:35px;
    	float:left;
    	border:1px solid #BBBBBB;
    }
    #quantityNum .button{
    	color:#555; 
    	font-size: 18px; 
    	text-align: center; 
    	padding: 0 4px;
   		height: 17px;
   		cursor: pointer;
   		background: #f2f2f2;
   		line-height: 17px;
   		display: block;
		-webkit-touch-callout: none;
		-webkit-user-select: none;
		-khtml-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;

    }
    #quantityNum .minus{
    	border-top: 1px solid #bbb;
    }
    #quantityNum .button:hover{
    	text-decoration: none !important;
    	opacity: 0.7;
    	-ms-filter:"progid:DXImageTransform.Microsoft.Alpha"(Opacity=70);
    }
    #quantityNum #packunit{
    	margin: 10px;
    	float:left;
    }
</style>
{/block}
