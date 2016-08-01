{extends file='parent:frontend/detail/tabs/description.tpl'}


		{* Links list *}
		{block name='frontend_detail_description_links_list'}
			<ul class="content--list list--unstyled">
				{block name='frontend_detail_actions_contact'}
					<li class="list--entry">
						 <a href="{$sInquiry}" rel="nofollow" class="content--link link--contact" title="{"{s name='DetailLinkContact' namespace="frontend/detail/actions"}{/s}"|escape}">
							<i class="icon--arrow-right"></i> {s name="DetailLinkContact" namespace="frontend/detail/actions"}{/s}
						</a>
					</li>
				{/block}

				{foreach $sArticle.sLinks as $information}
					{if $information.supplierSearch}

						{* Vendor landing page link *}
						{block name='frontend_detail_description_links_supplier'}{/block}
					{else}

						{* Links which will be added throught the administration *}
						{block name='frontend_detail_description_links_link'}
							<li class="list--entry">
								<i class="icon--chain"></i> <a href="{$information.link}"
								   target="{if $information.target}{$information.target}{else}_blank{/if}"
								   class="content--link link--further-links"
								   title="{$information.description}">
                                    <i class="icon--arrow-right"></i> {$information.description}
								</a>
							</li>
						{/block}
					{/if}
				{/foreach}
			</ul>
		{/block}