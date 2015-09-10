<?php

/* navbar_header.html */
class __TwigTemplate_fb395f4d20642ced20f4d66a6a6a77e3 extends Twig_Template
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
\t<div class=\"inventea-posts-menu\">
\t\t<ul class=\"inventea-menu\" role=\"menubar\">
\t\t\t";
        // line 4
        // line 5
        echo "
\t\t\t";
        // line 6
        if ((isset($context["S_DISPLAY_SEARCH"]) ? $context["S_DISPLAY_SEARCH"] : null)) {
            // line 7
            echo "\t\t\t\t";
            if ((isset($context["S_REGISTERED_USER"]) ? $context["S_REGISTERED_USER"] : null)) {
                echo "<li><a href=\"";
                echo (isset($context["U_SEARCH_SELF"]) ? $context["U_SEARCH_SELF"] : null);
                echo "\" role=\"menuitem\">";
                echo $this->env->getExtension('phpbb')->lang("SEARCH_SELF");
                echo "</a></li>";
            }
            // line 8
            echo "\t\t\t\t";
            if ((isset($context["S_USER_LOGGED_IN"]) ? $context["S_USER_LOGGED_IN"] : null)) {
                echo "<li><a href=\"";
                echo (isset($context["U_SEARCH_NEW"]) ? $context["U_SEARCH_NEW"] : null);
                echo "\" role=\"menuitem\">";
                echo $this->env->getExtension('phpbb')->lang("SEARCH_NEW");
                echo "</a></li>";
            }
            // line 9
            echo "\t\t\t\t";
            if ((isset($context["S_LOAD_UNREADS"]) ? $context["S_LOAD_UNREADS"] : null)) {
                echo "<li><a href=\"";
                echo (isset($context["U_SEARCH_UNREAD"]) ? $context["U_SEARCH_UNREAD"] : null);
                echo "\" role=\"menuitem\">";
                echo $this->env->getExtension('phpbb')->lang("SEARCH_UNREAD");
                echo "</a></li>";
            }
            // line 10
            echo "\t\t\t\t<li><a href=\"";
            echo (isset($context["U_SEARCH_UNANSWERED"]) ? $context["U_SEARCH_UNANSWERED"] : null);
            echo "\" role=\"menuitem\">";
            echo $this->env->getExtension('phpbb')->lang("SEARCH_UNANSWERED");
            echo "</a></li>
\t\t\t\t<li><a href=\"";
            // line 11
            echo (isset($context["U_SEARCH_ACTIVE_TOPICS"]) ? $context["U_SEARCH_ACTIVE_TOPICS"] : null);
            echo "\" role=\"menuitem\">";
            echo $this->env->getExtension('phpbb')->lang("SEARCH_ACTIVE_TOPICS");
            echo "</a></li>
\t\t\t";
        }
        // line 13
        echo "
\t\t\t";
        // line 14
        // line 15
        echo "\t\t</ul>
\t</div>

\t<div class=\"inventea-forum-menu\">
\t\t<ul class=\"inventea-menu\" role=\"menubar\">
\t\t\t";
        // line 20
        // line 21
        echo "
\t\t\t";
        // line 22
        if ((isset($context["S_DISPLAY_SEARCH"]) ? $context["S_DISPLAY_SEARCH"] : null)) {
            echo "<li><a href=\"";
            echo (isset($context["U_SEARCH"]) ? $context["U_SEARCH"] : null);
            echo "\" role=\"menuitem\">";
            echo $this->env->getExtension('phpbb')->lang("SEARCH");
            echo "</a></li>";
        }
        // line 23
        echo "

\t\t\t";
        // line 25
        if ((isset($context["U_MCP"]) ? $context["U_MCP"] : null)) {
            echo "<li><a href=\"";
            echo (isset($context["U_MCP"]) ? $context["U_MCP"] : null);
            echo "\" title=\"";
            echo $this->env->getExtension('phpbb')->lang("MCP_SHORT");
            echo "\" role=\"menuitem\">";
            echo $this->env->getExtension('phpbb')->lang("MCP");
            echo "</a></li>";
        }
        // line 26
        echo "\t\t\t";
        if ((isset($context["U_ACP"]) ? $context["U_ACP"] : null)) {
            echo "<li><a href=\"";
            echo (isset($context["U_ACP"]) ? $context["U_ACP"] : null);
            echo "\" title=\"";
            echo $this->env->getExtension('phpbb')->lang("ACP_SHORT");
            echo "\" role=\"menuitem\">";
            echo $this->env->getExtension('phpbb')->lang("ACP");
            echo "</a></li>";
        }
        // line 27
        echo "
\t\t\t";
        // line 28
        // line 29
        echo "\t\t</ul>
\t</div>
</nav>
";
    }

    public function getTemplateName()
    {
        return "navbar_header.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  123 => 29,  122 => 28,  108 => 26,  94 => 23,  86 => 22,  83 => 21,  82 => 20,  75 => 15,  64 => 11,  39 => 8,  30 => 7,  28 => 6,  25 => 5,  24 => 4,  170 => 35,  169 => 34,  166 => 33,  145 => 31,  142 => 30,  139 => 29,  121 => 27,  119 => 27,  116 => 25,  112 => 23,  105 => 21,  89 => 19,  80 => 18,  72 => 17,  63 => 15,  59 => 13,  52 => 11,  49 => 10,  26 => 6,  639 => 184,  636 => 183,  626 => 179,  622 => 177,  620 => 176,  616 => 174,  615 => 173,  612 => 172,  610 => 171,  604 => 170,  603 => 169,  590 => 168,  588 => 167,  584 => 166,  575 => 165,  573 => 164,  563 => 163,  560 => 162,  558 => 161,  555 => 160,  552 => 159,  542 => 157,  540 => 156,  536 => 154,  535 => 153,  526 => 147,  510 => 146,  506 => 144,  493 => 143,  492 => 142,  481 => 133,  478 => 132,  476 => 131,  468 => 129,  466 => 128,  457 => 127,  453 => 125,  448 => 122,  436 => 121,  424 => 120,  419 => 119,  416 => 118,  402 => 115,  399 => 114,  397 => 113,  394 => 112,  393 => 111,  382 => 107,  378 => 105,  377 => 104,  368 => 102,  360 => 101,  357 => 100,  356 => 99,  353 => 98,  345 => 97,  328 => 93,  325 => 92,  324 => 91,  317 => 90,  315 => 89,  311 => 87,  310 => 86,  307 => 85,  295 => 84,  287 => 78,  286 => 77,  272 => 75,  265 => 71,  261 => 69,  260 => 68,  255 => 66,  252 => 65,  243 => 61,  239 => 59,  231 => 57,  226 => 55,  218 => 53,  216 => 52,  209 => 50,  203 => 49,  199 => 48,  193 => 47,  173 => 31,  171 => 30,  161 => 22,  155 => 32,  117 => 15,  95 => 13,  84 => 12,  73 => 11,  62 => 10,  60 => 14,  35 => 7,  22 => 2,  256 => 66,  251 => 64,  240 => 57,  229 => 56,  225 => 54,  221 => 53,  215 => 49,  213 => 48,  207 => 44,  204 => 43,  190 => 46,  188 => 41,  179 => 37,  172 => 34,  165 => 31,  159 => 30,  156 => 29,  153 => 19,  150 => 18,  136 => 26,  132 => 16,  130 => 28,  127 => 23,  124 => 22,  115 => 24,  106 => 14,  104 => 19,  98 => 25,  91 => 17,  81 => 14,  74 => 14,  71 => 13,  65 => 16,  57 => 10,  48 => 9,  34 => 7,  31 => 6,  19 => 1,);
    }
}
