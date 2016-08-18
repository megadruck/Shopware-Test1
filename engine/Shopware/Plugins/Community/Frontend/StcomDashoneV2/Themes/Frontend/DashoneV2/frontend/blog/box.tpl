{extends file='parent:frontend/blog/box.tpl'}

{* Blog Box *}
{block name='frontend_blog_col_box_content'}
    <div class="blog--box-content panel--body is--wide block">

        {* Article pictures *}
        {block name='frontend_blog_col_article_picture'}
            {if $sArticle.media}
                <div class="blog--box-picture">
                    <a href="{url controller=blog action=detail sCategory=$sArticle.categoryId blogArticle=$sArticle.id}"
                       class="blog--picture-main"
                       title="{$sArticle.title|escape}">
                        {if isset($sArticle.media.thumbnails)}
                            <img srcset="{$sArticle.media.thumbnails[0].sourceSet}"
                                 alt="{$sArticle.title|escape}"
                                 title="{$sArticle.title|escape|truncate:25:""}"/>
                        {else}
                            <img src="{link file='frontend/_public/src/img/no-picture.jpg'}"
                                 alt="{$sArticle.title|escape}"
                                 title="{$sArticle.title|escape|truncate:25:""}"/>
                        {/if}
                    </a>
                </div>
            {/if}
        {/block}

        {* Article Description *}
        {block name='frontend_blog_col_description'}
            <div class="blog--box-description{if !$sArticle.media} is--fluid{/if}">

                {block name='frontend_blog_col_description_short'}
                    <div class="blog--box-description-short">
                        {if $sArticle.shortDescription}{$sArticle.shortDescription|nl2br}{else}{$sArticle.shortDescription}{/if}
                    </div>
                {/block}

                {* Read more button *}
                {block name='frontend_blog_col_read_more'}
                {/block}

                {* Tags *}
                {block name='frontend_blog_col_tags'}
                {/block}

            </div>
        {/block}

        {* Read more button *}
        {block name='frontend_blog_col_read_more'}
            <div class="blog--box-readmore">
                <a href="{url controller=blog action=detail sCategory=$sArticle.categoryId blogArticle=$sArticle.id}" title="{$sArticle.title|escape}"
                   class="btn is--primary is--small">{s name="BlogLinkMore"}{/s}</a>
            </div>
        {/block}

        {* Tags *}
        {block name='frontend_blog_col_tags'}
            <div class="blog--box-tags">
                {if $sArticle.tags|@count > 1}
                    <strong>{s name="BlogInfoTags"}{/s}</strong>
                    {foreach $sArticle.tags as $tag}
                        <a href="{$tag.link}" title="{$tag.name|escape}">{$tag.name}</a>{if !$tag@last}, {/if}
                    {/foreach}
                {/if}
            </div>
        {/block}

    </div>
{/block}