<?php

/* navbar_footer.html */
class __TwigTemplate_fe0e6841a78baca135301ffd4a4388d7 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<nav role=\"navigation\">
\t<ul id=\"nav-footer\" class=\"linklist\" role=\"menubar\">
\t\t<li class=\"small-icon breadcrumbs\">
\t\t\t";
        // line 4
        if ((isset($context["U_SITE_HOME"]) ? $context["U_SITE_HOME"] : null)) {
            echo "<span class=\"crumb\"><a href=\"";
            echo (isset($context["U_SITE_HOME"]) ? $context["U_SITE_HOME"] : null);
            echo "\" data-navbar-reference=\"home\">";
            echo $this->env->getExtension('phpbb')->lang("SITE_HOME");
            echo "</a></span>";
        }
        // line 5
        echo "\t\t\t";
        // line 6
        echo "\t\t\t<span class=\"crumb\"><a href=\"";
        echo (isset($context["U_INDEX"]) ? $context["U_INDEX"] : null);
        echo "\" data-navbar-reference=\"index\">";
        echo $this->env->getExtension('phpbb')->lang("INDEX");
        echo "</a></span>
\t\t\t";
        // line 7
        // line 8
        echo "\t\t</li>

\t\t";
        // line 10
        if (((isset($context["U_WATCH_FORUM_LINK"]) ? $context["U_WATCH_FORUM_LINK"] : null) && (!(isset($context["S_IS_BOT"]) ? $context["S_IS_BOT"] : null)))) {
            echo "<li class=\"small-icon icon-";
            if ((isset($context["S_WATCHING_FORUM"]) ? $context["S_WATCHING_FORUM"] : null)) {
                echo "unsubscribe";
            } else {
                echo "subscribe";
            }
            echo "\" data-last-responsive=\"true\"><a href=\"";
            echo (isset($context["U_WATCH_FORUM_LINK"]) ? $context["U_WATCH_FORUM_LINK"] : null);
            echo "\" title=\"";
            echo (isset($context["S_WATCH_FORUM_TITLE"]) ? $context["S_WATCH_FORUM_TITLE"] : null);
            echo "\" data-ajax=\"toggle_link\" data-toggle-class=\"small-icon icon-";
            if ((!(isset($context["S_WATCHING_FORUM"]) ? $context["S_WATCHING_FORUM"] : null))) {
                echo "unsubscribe";
            } else {
                echo "subscribe";
            }
            echo "\" data-toggle-text=\"";
            echo (isset($context["S_WATCH_FORUM_TOGGLE"]) ? $context["S_WATCH_FORUM_TOGGLE"] : null);
            echo "\" data-toggle-url=\"";
            echo (isset($context["U_WATCH_FORUM_TOGGLE"]) ? $context["U_WATCH_FORUM_TOGGLE"] : null);
            echo "\">";
            echo (isset($context["S_WATCH_FORUM_TITLE"]) ? $context["S_WATCH_FORUM_TITLE"] : null);
            echo "</a></li>";
        }
        // line 11
        echo "\t\t";
        if ((isset($context["U_ACP"]) ? $context["U_ACP"] : null)) {
            echo "<li class=\"small-icon\" data-last-responsive=\"true\"><a href=\"";
            echo (isset($context["U_ACP"]) ? $context["U_ACP"] : null);
            echo "\" title=\"";
            echo $this->env->getExtension('phpbb')->lang("ACP");
            echo "\" role=\"menuitem\">";
            echo $this->env->getExtension('phpbb')->lang("ACP_SHORT");
            echo "</a></li>";
        }
        // line 12
        echo "\t\t";
        if ((isset($context["U_CONTACT_US"]) ? $context["U_CONTACT_US"] : null)) {
            echo "<li class=\"small-icon\" data-last-responsive=\"true\"><a href=\"";
            echo (isset($context["U_CONTACT_US"]) ? $context["U_CONTACT_US"] : null);
            echo "\" role=\"menuitem\">";
            echo $this->env->getExtension('phpbb')->lang("CONTACT_US");
            echo "</a></li>";
        }
        // line 13
        echo "
\t\t";
        // line 14
        // line 15
        echo "\t\t";
        // line 16
        echo "
\t\t";
        // line 17
        if ((!(isset($context["S_IS_BOT"]) ? $context["S_IS_BOT"] : null))) {
            echo "<li class=\"small-icon\"><a href=\"";
            echo (isset($context["U_DELETE_COOKIES"]) ? $context["U_DELETE_COOKIES"] : null);
            echo "\" data-ajax=\"true\" data-refresh=\"true\" role=\"menuitem\">";
            echo $this->env->getExtension('phpbb')->lang("DELETE_COOKIES");
            echo "</a></li>";
        }
        // line 18
        echo "
\t\t";
        // line 19
        // line 20
        echo "\t\t<li>";
        echo (isset($context["S_TIMEZONE"]) ? $context["S_TIMEZONE"] : null);
        echo "</li>
\t\t";
        // line 21
        // line 22
        echo "\t</ul>
</nav>
";
    }

    public function getTemplateName()
    {
        return "navbar_footer.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  118 => 21,  96 => 15,  92 => 13,  42 => 8,  32 => 5,  185 => 48,  184 => 47,  162 => 43,  158 => 41,  143 => 40,  128 => 39,  113 => 20,  109 => 18,  101 => 17,  87 => 24,  66 => 16,  56 => 13,  51 => 12,  46 => 10,  45 => 10,  41 => 7,  29 => 7,  23 => 3,  21 => 2,  123 => 29,  122 => 28,  108 => 26,  94 => 23,  86 => 22,  83 => 12,  82 => 20,  75 => 15,  64 => 11,  39 => 8,  30 => 5,  28 => 6,  25 => 5,  24 => 4,  170 => 35,  169 => 34,  166 => 33,  145 => 31,  142 => 30,  139 => 29,  121 => 38,  119 => 22,  116 => 25,  112 => 19,  105 => 34,  89 => 19,  80 => 18,  72 => 11,  63 => 15,  59 => 14,  52 => 11,  49 => 10,  26 => 3,  639 => 184,  636 => 183,  626 => 179,  622 => 177,  620 => 176,  616 => 174,  615 => 173,  612 => 172,  610 => 171,  604 => 170,  603 => 169,  590 => 168,  588 => 167,  584 => 166,  575 => 165,  573 => 164,  563 => 163,  560 => 162,  558 => 161,  555 => 160,  552 => 159,  542 => 157,  540 => 156,  536 => 154,  535 => 153,  526 => 147,  510 => 146,  506 => 144,  493 => 143,  492 => 142,  481 => 133,  478 => 132,  476 => 131,  468 => 129,  466 => 128,  457 => 127,  453 => 125,  448 => 122,  436 => 121,  424 => 120,  419 => 119,  416 => 118,  402 => 115,  399 => 114,  397 => 113,  394 => 112,  393 => 111,  382 => 107,  378 => 105,  377 => 104,  368 => 102,  360 => 101,  357 => 100,  356 => 99,  353 => 98,  345 => 97,  328 => 93,  325 => 92,  324 => 91,  317 => 90,  315 => 89,  311 => 87,  310 => 86,  307 => 85,  295 => 84,  287 => 78,  286 => 77,  272 => 75,  265 => 71,  261 => 69,  260 => 68,  255 => 66,  252 => 65,  243 => 61,  239 => 59,  231 => 57,  226 => 55,  218 => 53,  216 => 52,  209 => 50,  203 => 49,  199 => 48,  193 => 47,  173 => 31,  171 => 30,  161 => 42,  155 => 32,  117 => 15,  95 => 14,  84 => 12,  73 => 11,  62 => 10,  60 => 15,  35 => 7,  22 => 2,  256 => 66,  251 => 64,  240 => 57,  229 => 56,  225 => 54,  221 => 53,  215 => 49,  213 => 48,  207 => 44,  204 => 43,  190 => 46,  188 => 41,  179 => 45,  172 => 34,  165 => 44,  159 => 30,  156 => 29,  153 => 19,  150 => 18,  136 => 26,  132 => 16,  130 => 28,  127 => 23,  124 => 22,  115 => 24,  106 => 14,  104 => 19,  98 => 16,  91 => 17,  81 => 14,  74 => 14,  71 => 13,  65 => 16,  57 => 10,  48 => 9,  34 => 6,  31 => 6,  19 => 1,);
    }
}
