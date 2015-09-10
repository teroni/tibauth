<?php

/* navbar_responsive_header.html */
class __TwigTemplate_8b4d0e0da24d2f45e379e0f405c4229b extends Twig_Template
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
        echo "<div class=\"dropdown-container hidden inventea-mobile-dropdown-menu\">
    <a href=\"#\" class=\"dropdown-trigger inventea-toggle\"><i class=\"fa fa-bars\"></i></a>
    <div class=\"dropdown hidden\">
        <div class=\"pointer\"><div class=\"pointer-inner\"></div></div>
        <ul class=\"dropdown-contents\" role=\"menubar\">
            ";
        // line 6
        if ((isset($context["U_SITE_HOME"]) ? $context["U_SITE_HOME"] : null)) {
            echo "<li><a href=\"";
            echo (isset($context["U_SITE_HOME"]) ? $context["U_SITE_HOME"] : null);
            echo "\" role=\"menuitem\"><i class=\"fa fa-fw fa-home\"></i> ";
            echo $this->env->getExtension('phpbb')->lang("SITE_HOME");
            echo "</a></li>";
        }
        // line 7
        echo "            <li><a href=\"";
        echo (isset($context["U_INDEX"]) ? $context["U_INDEX"] : null);
        echo "\" role=\"menuitem\"><i class=\"fa fa-fw ";
        if ((isset($context["U_SITE_HOME"]) ? $context["U_SITE_HOME"] : null)) {
            echo "fa-globe";
        } else {
            echo "fa-home";
        }
        echo "\"></i> ";
        echo $this->env->getExtension('phpbb')->lang("INDEX");
        echo "</a></li>

            ";
        // line 9
        // line 10
        echo "
            <li><a href=\"";
        // line 11
        echo (isset($context["U_FAQ"]) ? $context["U_FAQ"] : null);
        echo "\" role=\"menuitem\"><i class=\"fa fa-fw fa-info-circle\"></i> ";
        echo $this->env->getExtension('phpbb')->lang("FAQ");
        echo "</a></li>

            ";
        // line 13
        // line 14
        echo "
            ";
        // line 15
        if ((isset($context["S_DISPLAY_SEARCH"]) ? $context["S_DISPLAY_SEARCH"] : null)) {
            // line 16
            echo "                <li><a href=\"";
            echo (isset($context["U_SEARCH"]) ? $context["U_SEARCH"] : null);
            echo "\" role=\"menuitem\"><i class=\"fa fa-fw fa-search\"></i> ";
            echo $this->env->getExtension('phpbb')->lang("SEARCH");
            echo "</a></li>
                ";
            // line 17
            if ((isset($context["S_REGISTERED_USER"]) ? $context["S_REGISTERED_USER"] : null)) {
                echo "<li><a href=\"";
                echo (isset($context["U_SEARCH_SELF"]) ? $context["U_SEARCH_SELF"] : null);
                echo "\" role=\"menuitem\"><i class=\"fa fa-fw fa-comments-o\"></i> ";
                echo $this->env->getExtension('phpbb')->lang("SEARCH_SELF");
                echo "</a></li>";
            }
            // line 18
            echo "                ";
            if ((isset($context["S_USER_LOGGED_IN"]) ? $context["S_USER_LOGGED_IN"] : null)) {
                echo "<li><a href=\"";
                echo (isset($context["U_SEARCH_NEW"]) ? $context["U_SEARCH_NEW"] : null);
                echo "\" role=\"menuitem\"><i class=\"fa fa-fw fa-comment\"></i> ";
                echo $this->env->getExtension('phpbb')->lang("SEARCH_NEW");
                echo "</a></li>";
            }
            // line 19
            echo "                ";
            if ((isset($context["S_LOAD_UNREADS"]) ? $context["S_LOAD_UNREADS"] : null)) {
                echo "<li><a href=\"";
                echo (isset($context["U_SEARCH_UNREAD"]) ? $context["U_SEARCH_UNREAD"] : null);
                echo "\" role=\"menuitem\"><i class=\"fa fa-fw fa-comments\"></i> ";
                echo $this->env->getExtension('phpbb')->lang("SEARCH_UNREAD");
                echo "</a></li>";
            }
            // line 20
            echo "                <li><a href=\"";
            echo (isset($context["U_SEARCH_UNANSWERED"]) ? $context["U_SEARCH_UNANSWERED"] : null);
            echo "\" role=\"menuitem\"><i class=\"fa fa-fw fa-comment-o\"></i> ";
            echo $this->env->getExtension('phpbb')->lang("SEARCH_UNANSWERED");
            echo "</a></li>
                <li><a href=\"";
            // line 21
            echo (isset($context["U_SEARCH_ACTIVE_TOPICS"]) ? $context["U_SEARCH_ACTIVE_TOPICS"] : null);
            echo "\" role=\"menuitem\"><i class=\"fa fa-fw fa-check-circle-o\"></i> ";
            echo $this->env->getExtension('phpbb')->lang("SEARCH_ACTIVE_TOPICS");
            echo "</a></li>
            ";
        }
        // line 23
        echo "
            ";
        // line 24
        // line 25
        echo "
            ";
        // line 26
        if ((!(isset($context["S_IS_BOT"]) ? $context["S_IS_BOT"] : null))) {
            // line 27
            echo "                ";
            if ((isset($context["S_DISPLAY_MEMBERLIST"]) ? $context["S_DISPLAY_MEMBERLIST"] : null)) {
                echo "<li><a href=\"";
                echo (isset($context["U_MEMBERLIST"]) ? $context["U_MEMBERLIST"] : null);
                echo "\" role=\"menuitem\"><i class=\"fa fa-fw fa-users\"></i> ";
                echo $this->env->getExtension('phpbb')->lang("MEMBERLIST");
                echo "</a></li>";
            }
            // line 28
            echo "                ";
            if ((isset($context["U_TEAM"]) ? $context["U_TEAM"] : null)) {
                echo "<li><a href=\"";
                echo (isset($context["U_TEAM"]) ? $context["U_TEAM"] : null);
                echo "\" role=\"menuitem\"><i class=\"fa fa-fw fa-shield\"></i> ";
                echo $this->env->getExtension('phpbb')->lang("THE_TEAM");
                echo "</a></li>";
            }
            // line 29
            echo "            ";
        }
        // line 30
        echo "
            ";
        // line 31
        if ((isset($context["U_MCP"]) ? $context["U_MCP"] : null)) {
            echo "<li><a href=\"";
            echo (isset($context["U_MCP"]) ? $context["U_MCP"] : null);
            echo "\" title=\"";
            echo $this->env->getExtension('phpbb')->lang("MCP_SHORT");
            echo "\" role=\"menuitem\"><i class=\"fa fa-fw fa-tachometer\"></i> ";
            echo $this->env->getExtension('phpbb')->lang("MCP");
            echo "</a></li>";
        }
        // line 32
        echo "            ";
        if ((isset($context["U_ACP"]) ? $context["U_ACP"] : null)) {
            echo "<li><a href=\"";
            echo (isset($context["U_ACP"]) ? $context["U_ACP"] : null);
            echo "\" title=\"";
            echo $this->env->getExtension('phpbb')->lang("ACP_SHORT");
            echo "\" role=\"menuitem\"><i class=\"fa fa-fw fa-cog\"></i> ";
            echo $this->env->getExtension('phpbb')->lang("ACP");
            echo "</a></li>";
        }
        // line 33
        echo "
            ";
        // line 34
        // line 35
        echo "        </ul>
    </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "navbar_responsive_header.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  170 => 35,  169 => 34,  166 => 33,  145 => 31,  142 => 30,  139 => 29,  121 => 27,  119 => 26,  116 => 25,  112 => 23,  105 => 21,  89 => 19,  80 => 18,  72 => 17,  63 => 15,  59 => 13,  52 => 11,  49 => 10,  26 => 6,  639 => 184,  636 => 183,  626 => 179,  622 => 177,  620 => 176,  616 => 174,  615 => 173,  612 => 172,  610 => 171,  604 => 170,  603 => 169,  590 => 168,  588 => 167,  584 => 166,  575 => 165,  573 => 164,  563 => 163,  560 => 162,  558 => 161,  555 => 160,  552 => 159,  542 => 157,  540 => 156,  536 => 154,  535 => 153,  526 => 147,  510 => 146,  506 => 144,  493 => 143,  492 => 142,  481 => 133,  478 => 132,  476 => 131,  468 => 129,  466 => 128,  457 => 127,  453 => 125,  448 => 122,  436 => 121,  424 => 120,  419 => 119,  416 => 118,  402 => 115,  399 => 114,  397 => 113,  394 => 112,  393 => 111,  382 => 107,  378 => 105,  377 => 104,  368 => 102,  360 => 101,  357 => 100,  356 => 99,  353 => 98,  345 => 97,  328 => 93,  325 => 92,  324 => 91,  317 => 90,  315 => 89,  311 => 87,  310 => 86,  307 => 85,  295 => 84,  287 => 78,  286 => 77,  272 => 75,  265 => 71,  261 => 69,  260 => 68,  255 => 66,  252 => 65,  243 => 61,  239 => 59,  231 => 57,  226 => 55,  218 => 53,  216 => 52,  209 => 50,  203 => 49,  199 => 48,  193 => 47,  173 => 31,  171 => 30,  161 => 22,  155 => 32,  117 => 15,  95 => 13,  84 => 12,  73 => 11,  62 => 10,  60 => 14,  35 => 7,  22 => 2,  256 => 66,  251 => 64,  240 => 57,  229 => 56,  225 => 54,  221 => 53,  215 => 49,  213 => 48,  207 => 44,  204 => 43,  190 => 46,  188 => 41,  179 => 37,  172 => 34,  165 => 31,  159 => 30,  156 => 29,  153 => 19,  150 => 18,  136 => 26,  132 => 16,  130 => 28,  127 => 23,  124 => 22,  115 => 24,  106 => 14,  104 => 19,  98 => 20,  91 => 17,  81 => 14,  74 => 13,  71 => 12,  65 => 16,  57 => 10,  48 => 9,  34 => 7,  31 => 6,  19 => 1,);
    }
}
