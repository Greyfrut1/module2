<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* modules/custom/greyfrut_module2/templates/reviews-list.html.twig */
class __TwigTemplate_77615c9464771cae86b06e418e2cd333 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<div>
  <ul class=\"reviews-list\">
    ";
        // line 3
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["rows"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["row"]) {
            // line 4
            echo "      <li class=\"reviews-list_item\">
        <article>
          <div class=\"reviews-list_item__block1\">
            <div>";
            // line 7
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["row"], "avatar", [], "any", false, false, true, 7), 7, $this->source), "html", null, true);
            echo "</div>
            <span>";
            // line 8
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["row"], "name", [], "any", false, false, true, 8), 8, $this->source), "html", null, true);
            echo "</span>
            <span>";
            // line 9
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["row"], "created", [], "any", false, false, true, 9), 9, $this->source), "html", null, true);
            echo "</span>
            ";
            // line 10
            if ((twig_get_attribute($this->env, $this->source, $context["row"], "user", [], "any", false, false, true, 10) == "administrator")) {
                // line 11
                echo "              <div>
                <a class=\"button\" href=\"";
                // line 12
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["row"], "edit", [], "any", false, false, true, 12), 12, $this->source), "html", null, true);
                echo "\">Edit</a>
                <a href=\"";
                // line 13
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["row"], "delete", [], "any", false, false, true, 13), 13, $this->source), "html", null, true);
                echo "\" class=\"button use-ajax button-delete delete-button-class\"
                   data-dialog-type=\"modal\" data-dialog-options=\"{&quot;width&quot;:400}\">Delete</a>
              </div>
            ";
            }
            // line 17
            echo "          </div>
          <div class=\"reviews-list_item__block2\">
            ";
            // line 19
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["row"], "image", [], "any", false, false, true, 19), 19, $this->source), "html", null, true);
            echo "
            <p>";
            // line 20
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["row"], "review_text", [], "any", false, false, true, 20), 20, $this->source), "html", null, true);
            echo "</p>
          </div>
          <div class=\"reviews-list_item__block3\">
            <span>Telephone: <a href=\"tel: ";
            // line 23
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["row"], "phone_number", [], "any", false, false, true, 23), 23, $this->source), "html", null, true);
            echo "\">";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["row"], "phone_number", [], "any", false, false, true, 23), 23, $this->source), "html", null, true);
            echo "</a></span>
            <span>Email: <a href=\"mailto: ";
            // line 24
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["row"], "email", [], "any", false, false, true, 24), 24, $this->source), "html", null, true);
            echo "\">";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["row"], "email", [], "any", false, false, true, 24), 24, $this->source), "html", null, true);
            echo "</a></span>
          </div>
        </article>
      </li>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['row'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 29
        echo "  </ul>
</div>
";
    }

    public function getTemplateName()
    {
        return "modules/custom/greyfrut_module2/templates/reviews-list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  113 => 29,  100 => 24,  94 => 23,  88 => 20,  84 => 19,  80 => 17,  73 => 13,  69 => 12,  66 => 11,  64 => 10,  60 => 9,  56 => 8,  52 => 7,  47 => 4,  43 => 3,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "modules/custom/greyfrut_module2/templates/reviews-list.html.twig", "/var/www/web/modules/custom/greyfrut_module2/templates/reviews-list.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("for" => 3, "if" => 10);
        static $filters = array("escape" => 7);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['for', 'if'],
                ['escape'],
                []
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
