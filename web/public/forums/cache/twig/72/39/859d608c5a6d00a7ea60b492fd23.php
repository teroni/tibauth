<?php

/* viewtopic_topic_tools.html */
class __TwigTemplate_7239859d608c5a6d00a7ea60b492fd23 extends Twig_Template
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
        if (((!(isset($context["S_IS_BOT"]) ? $context["S_IS_BOT"] : null)) && ((((((isset($context["U_WATCH_TOPIC"]) ? $context["U_WATCH_TOPIC"] : null) || (isset($context["U_BOOKMARK_TOPIC"]) ? $context["U_BOOKMARK_TOPIC"] : null)) || (isset($context["U_BUMP_TOPIC"]) ? $context["U_BUMP_TOPIC"] : null)) || (isset($context["U_EMAIL_TOPIC"]) ? $context["U_EMAIL_TOPIC"] : null)) || (isset($context["U_PRINT_TOPIC"]) ? $context["U_PRINT_TOPIC"] : null)) || (isset($context["S_DISPLAY_TOPIC_TOOLS"]) ? $context["S_DISPLAY_TOPIC_TOOLS"] : null)))) {
            // line 2
            echo "\t<div class=\"dropdown-container dropdown-button-control topic-tools\">
\t\t<span title=\"";
            // line 3
            echo $this->env->getExtension('phpbb')->lang("TOPIC_TOOLS");
            echo "\" class=\"button icon-button tools-icon dropdown-trigger dropdown-select\"></span>
\t\t<div class=\"dropdown hidden\">
\t\t\t<div class=\"pointer\"><div class=\"pointer-inner\"></div></div>
\t\t\t<ul class=\"dropdown-contents\">
\t\t\t\t";
            // line 7
            // line 8
            echo "\t\t\t\t";
            if ((isset($context["U_WATCH_TOPIC"]) ? $context["U_WATCH_TOPIC"] : null)) {
                // line 9
                echo "\t\t\t\t\t<li class=\"small-icon icon-";
                if ((isset($context["S_WATCHING_TOPIC"]) ? $context["S_WATCHING_TOPIC"] : null)) {
                    echo "unsubscribe";
                } else {
                    echo "subscribe";
                }
                echo "\">
\t\t\t\t\t\t<a href=\"";
                // line 10
                echo (isset($context["U_WATCH_TOPIC"]) ? $context["U_WATCH_TOPIC"] : null);
                echo "\" class=\"watch-topic-link\" title=\"";
                echo (isset($context["S_WATCH_TOPIC_TITLE"]) ? $context["S_WATCH_TOPIC_TITLE"] : null);
                echo "\" data-ajax=\"toggle_link\" data-toggle-class=\"small-icon icon-";
                if ((!(isset($context["S_WATCHING_TOPIC"]) ? $context["S_WATCHING_TOPIC"] : null))) {
                    echo "unsubscribe";
                } else {
                    echo "subscribe";
                }
                echo "\" data-toggle-text=\"";
                echo (isset($context["S_WATCH_TOPIC_TOGGLE"]) ? $context["S_WATCH_TOPIC_TOGGLE"] : null);
                echo "\" data-toggle-url=\"";
                echo (isset($context["U_WATCH_TOPIC_TOGGLE"]) ? $context["U_WATCH_TOPIC_TOGGLE"] : null);
                echo "\" data-update-all=\".watch-topic-link\">";
                echo (isset($context["S_WATCH_TOPIC_TITLE"]) ? $context["S_WATCH_TOPIC_TITLE"] : null);
                echo "</a>
\t\t\t\t\t</li>
\t\t\t\t";
            }
            // line 13
            echo "\t\t\t\t";
            if ((isset($context["U_BOOKMARK_TOPIC"]) ? $context["U_BOOKMARK_TOPIC"] : null)) {
                // line 14
                echo "\t\t\t\t\t<li class=\"small-icon icon-bookmark\">
\t\t\t\t\t\t<a href=\"";
                // line 15
                echo (isset($context["U_BOOKMARK_TOPIC"]) ? $context["U_BOOKMARK_TOPIC"] : null);
                echo "\" class=\"bookmark-link\" title=\"";
                echo $this->env->getExtension('phpbb')->lang("BOOKMARK_TOPIC");
                echo "\" data-ajax=\"alt_text\" data-alt-text=\"";
                echo (isset($context["S_BOOKMARK_TOGGLE"]) ? $context["S_BOOKMARK_TOGGLE"] : null);
                echo "\" data-update-all=\".bookmark-link\">";
                echo (isset($context["S_BOOKMARK_TOPIC"]) ? $context["S_BOOKMARK_TOPIC"] : null);
                echo "</a>
\t\t\t\t\t</li>
\t\t\t\t";
            }
            // line 18
            echo "\t\t\t\t";
            if ((isset($context["U_BUMP_TOPIC"]) ? $context["U_BUMP_TOPIC"] : null)) {
                echo "<li class=\"small-icon icon-bump\"><a href=\"";
                echo (isset($context["U_BUMP_TOPIC"]) ? $context["U_BUMP_TOPIC"] : null);
                echo "\" title=\"";
                echo $this->env->getExtension('phpbb')->lang("BUMP_TOPIC");
                echo "\" data-ajax=\"true\">";
                echo $this->env->getExtension('phpbb')->lang("BUMP_TOPIC");
                echo "</a></li>";
            }
            // line 19
            echo "\t\t\t\t";
            if ((isset($context["U_EMAIL_TOPIC"]) ? $context["U_EMAIL_TOPIC"] : null)) {
                echo "<li class=\"small-icon icon-sendemail\"><a href=\"";
                echo (isset($context["U_EMAIL_TOPIC"]) ? $context["U_EMAIL_TOPIC"] : null);
                echo "\" title=\"";
                echo $this->env->getExtension('phpbb')->lang("EMAIL_TOPIC");
                echo "\">";
                echo $this->env->getExtension('phpbb')->lang("EMAIL_TOPIC");
                echo "</a></li>";
            }
            // line 20
            echo "\t\t\t\t";
            if ((isset($context["U_PRINT_TOPIC"]) ? $context["U_PRINT_TOPIC"] : null)) {
                echo "<li class=\"small-icon icon-print\"><a href=\"";
                echo (isset($context["U_PRINT_TOPIC"]) ? $context["U_PRINT_TOPIC"] : null);
                echo "\" title=\"";
                echo $this->env->getExtension('phpbb')->lang("PRINT_TOPIC");
                echo "\" accesskey=\"p\">";
                echo $this->env->getExtension('phpbb')->lang("PRINT_TOPIC");
                echo "</a></li>";
            }
            // line 21
            echo "\t\t\t\t";
            // line 22
            echo "\t\t\t</ul>
\t\t</div>
\t</div>
";
        }
    }

    public function getTemplateName()
    {
        return "viewtopic_topic_tools.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  115 => 21,  93 => 19,  82 => 18,  67 => 14,  64 => 13,  44 => 10,  35 => 9,  32 => 8,  24 => 3,  21 => 2,  1466 => 406,  1463 => 405,  1457 => 402,  1445 => 401,  1442 => 400,  1440 => 399,  1437 => 398,  1425 => 397,  1424 => 396,  1419 => 393,  1415 => 391,  1409 => 389,  1406 => 388,  1393 => 387,  1391 => 386,  1387 => 385,  1384 => 384,  1382 => 383,  1379 => 382,  1373 => 378,  1358 => 376,  1354 => 375,  1350 => 374,  1341 => 370,  1334 => 369,  1332 => 368,  1329 => 367,  1317 => 366,  1313 => 364,  1312 => 363,  1309 => 362,  1305 => 360,  1299 => 359,  1282 => 358,  1280 => 357,  1277 => 356,  1276 => 355,  1272 => 353,  1271 => 352,  1268 => 351,  1262 => 347,  1257 => 345,  1249 => 344,  1241 => 343,  1239 => 342,  1233 => 340,  1231 => 339,  1228 => 338,  1214 => 336,  1212 => 335,  1209 => 334,  1204 => 332,  1195 => 326,  1189 => 322,  1188 => 321,  1185 => 320,  1176 => 319,  1174 => 318,  1168 => 317,  1165 => 316,  1161 => 314,  1152 => 313,  1148 => 312,  1145 => 311,  1141 => 309,  1132 => 308,  1128 => 307,  1125 => 306,  1122 => 305,  1115 => 304,  1114 => 303,  1111 => 302,  1107 => 300,  1098 => 298,  1094 => 297,  1089 => 295,  1085 => 293,  1083 => 292,  1078 => 290,  1075 => 289,  1067 => 286,  1064 => 285,  1062 => 284,  1059 => 283,  1052 => 279,  1048 => 278,  1044 => 277,  1040 => 276,  1036 => 275,  1030 => 273,  1023 => 269,  1019 => 268,  1015 => 267,  1011 => 266,  1007 => 265,  1001 => 263,  999 => 262,  996 => 261,  995 => 260,  976 => 259,  975 => 258,  972 => 257,  970 => 256,  967 => 255,  963 => 253,  961 => 252,  951 => 249,  948 => 248,  945 => 247,  935 => 244,  932 => 243,  929 => 242,  919 => 239,  916 => 238,  913 => 237,  903 => 234,  900 => 233,  897 => 232,  887 => 229,  884 => 228,  881 => 227,  871 => 224,  868 => 223,  865 => 222,  864 => 221,  861 => 220,  858 => 219,  855 => 218,  854 => 217,  832 => 215,  822 => 213,  819 => 212,  813 => 209,  809 => 208,  804 => 207,  798 => 204,  794 => 203,  789 => 202,  786 => 201,  784 => 200,  778 => 196,  776 => 195,  769 => 190,  763 => 189,  759 => 187,  757 => 186,  750 => 184,  732 => 183,  728 => 181,  725 => 180,  721 => 179,  718 => 178,  714 => 177,  705 => 173,  699 => 171,  696 => 170,  693 => 169,  692 => 168,  689 => 167,  687 => 166,  681 => 165,  670 => 163,  667 => 162,  662 => 161,  661 => 160,  658 => 159,  650 => 157,  647 => 156,  645 => 155,  642 => 154,  632 => 153,  622 => 152,  605 => 151,  602 => 150,  592 => 149,  588 => 147,  586 => 146,  577 => 145,  576 => 144,  573 => 143,  571 => 142,  568 => 141,  555 => 140,  552 => 139,  551 => 138,  547 => 136,  541 => 134,  539 => 133,  525 => 132,  517 => 131,  489 => 128,  479 => 126,  476 => 125,  474 => 124,  470 => 123,  467 => 122,  458 => 116,  454 => 115,  447 => 111,  444 => 110,  436 => 107,  432 => 105,  430 => 104,  427 => 103,  421 => 100,  417 => 98,  415 => 97,  406 => 94,  399 => 92,  396 => 91,  390 => 90,  389 => 89,  376 => 87,  353 => 86,  327 => 85,  315 => 84,  297 => 83,  295 => 82,  291 => 81,  277 => 78,  273 => 77,  263 => 71,  261 => 70,  257 => 68,  255 => 67,  251 => 65,  245 => 63,  242 => 62,  229 => 61,  227 => 60,  216 => 59,  213 => 58,  211 => 57,  208 => 56,  200 => 51,  192 => 50,  186 => 49,  182 => 48,  177 => 46,  174 => 45,  172 => 44,  169 => 43,  157 => 42,  153 => 40,  152 => 39,  149 => 38,  145 => 36,  139 => 35,  122 => 34,  120 => 33,  117 => 22,  116 => 31,  110 => 27,  104 => 20,  99 => 21,  94 => 20,  86 => 18,  84 => 17,  75 => 14,  73 => 13,  70 => 15,  57 => 9,  54 => 8,  52 => 7,  49 => 6,  43 => 5,  34 => 3,  31 => 7,  19 => 1,);
    }
}
