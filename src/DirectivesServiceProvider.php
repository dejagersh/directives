<?php

namespace Jager\Directives;

use File;

use Illuminate\Support\ServiceProvider;

class DirectivesServiceProvider extends ServiceProvider
{
    public function fetchExpression($view, $start, $max) {
        $parenthesesCount = 1;
        $i = $start;

        // Iterate until the number of encountered opening parentheses matches number of passed closing parentheses
        while($i < $max && $parenthesesCount != 0) {
            if($view[$i] == '(') {
                $parenthesesCount++;
            } else if($view[$i] == ')') {
                $parenthesesCount--;
            }
            $i++;
        }

        return substr($view, $start, $i - $start - 1);
    }


    public function boot() {
        $directivesDirectory = base_path() . '/resources/views/directives';

        // Check if directory exists
        if(!File::exists($directivesDirectory)) {
            return;
        }

        $directivePaths = File::files($directivesDirectory);

        // Check if we have at least one file
        if(empty($directivePaths)) {
            return;
        }

        $regex = $this->buildRegex($directivePaths);

        \Blade::extend(function($view) use($regex) {
            $offset = 0;

            while(preg_match($regex, $view, $matches, PREG_OFFSET_CAPTURE, $offset)) {

                // Store directive name
                $directiveName = $matches[1][0];

                // Store start and length of pattern
                $patternStart = $matches[0][1];
                $patternLength = strlen($matches[0][0]);
                $expressionStart = $matches[2][1];

                // Fetch expression
                $expr = $this->fetchExpression($view, $expressionStart, $patternStart + $patternLength);

                // Store beginning and end
                $beginning = substr($view, 0, $patternStart);
                $end = substr($view, $expressionStart + strlen($expr) + 1);

                // Construct view
                $view = $beginning . "@include('directives.$directiveName', array('param' => ($expr)))" . $end;

                // Compute new offset to search from
                $offset = $patternStart + strlen($expr);
            }
            return $view;
        });
    }

    private function buildRegex($directivePaths) {
        // Start of regex
        $regex = '/\@(';

        // Length of the '.blade.php' extension
        $extensionLength = strlen('.blade.php');

        // Build list of directive names
        $directiveNames = [];
        foreach($directivePaths as $directivePath) {
            $fileName = basename($directivePath);
            $directiveName = substr($fileName, 0, strlen($fileName) - $extensionLength);

            $directiveNames[] = $directiveName;
        }

        // Add list of directives to regex
        $regex .= implode('|', $directiveNames);

        // Finish regex
        $regex .= ')\((.+)\)/';

        return $regex;
    }

    public function register() {

    }
}
