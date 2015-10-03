{blocks->getVars assign="vars"}
{if isset($vars.html5)}
    {if $vars.html5 == "header_open"}
        <header>
    {/if}
    {if $vars.html5 == "header_close"}
        </header>
    {/if}
    {if $vars.html5 == "article_open"}
        <article>
    {/if}
    {if $vars.html5 == "article_close"}
        </article>
    {/if}
    {if $vars.html5 == "aside_open"}
        <aside>
    {/if}
    {if $vars.html5 == "aside_close"}
        </aside>
    {/if}
    {if $vars.html5 == "footer_open"}
        <footer>
    {/if}
    {if $vars.html5 == "footer_close"}
        </footer>
    {/if}
    {if $vars.html5 == "section_open"}
        <section>
    {/if}
    {if $vars.html5 == "section_close"}
        </section>
    {/if}
    {if $vars.html5 == "nav_open"}
        <nav>
    {/if}
    {if $vars.html5 == "nav_close"}
        </nav>
    {/if}
    {$vars.text}
{/if}