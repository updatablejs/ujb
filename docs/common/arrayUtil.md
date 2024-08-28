

<h1>ArrayUtil</h1>

<h2>Description</h2>
<p>The <code>ArrayUtil</code> class provides utilities for manipulating arrays in PHP. These methods include checking if an array has numeric keys, and moving values up and down within arrays.</p>

<h2>Methods</h2>

<h3>isNumeric</h3>
<h4>Description:</h4>
<p>Checks if an array is a numeric array (i.e., has consecutive numeric keys).</p>

<h4>Signature:</h4>
<pre><code>public static function isNumeric($array): bool</code></pre>

<h4>Parameters:</h4>
<ul>
<li><code>$array</code> (mixed): The value to check if it is a numeric array.</li>
</ul>

<h4>Returns:</h4>
<p><code>bool</code>: <code>true</code> if the array is numeric, <code>false</code> otherwise.</p>

<h4>Usage Example:</h4>
<pre><code>
$array = [1, 2, 3];
$result = ArrayUtil::isNumeric($array); // returns true

$array = ['a' => 1, 'b' => 2];
$result = ArrayUtil::isNumeric($array); // returns false
</code></pre>

<h3>moveValuesUp</h3>
<h4>Description:</h4>
<p>Moves specified values up within an array.</p>

<h4>Signature:</h4>
<pre><code>public static function moveValuesUp(array $array, array $values): array</code></pre>

<h4>Parameters:</h4>
<ul>
    <li><code>$array</code> (array): The initial array.</li>
    <li><code>$values</code> (array): The values to be moved up.</li>
</ul>

<h4>Returns:</h4>
<p><code>array</code>: The modified array with values moved up.</p>

<h4>Usage Example:</h4>
<pre><code>
$array = [1, 2, 3, 4];
$values = [3, 4];
$result = ArrayUtil::moveValuesUp($array, $values); // returns [3, 4, 1, 2]
</code></pre>

<h3>moveValuesDown</h3>
<h4>Description:</h4>
<p>Moves specified values down within an array.</p>

<h4>Signature:</h4>
<pre><code>public static function moveValuesDown(array $array, array $values): array</code></pre>

<h4>Parameters:</h4>
<ul>
<li><code>$array</code> (array): The initial array.</li>
<li><code>$values</code> (array): The values to be moved down.</li>
</ul>

<h4>Returns:</h4>
<p><code>array</code>: The modified array with values moved down.</p>

<h4>Usage Example:</h4>
    <pre><code>
$array = [1, 2, 3, 4];
$values = [1, 2];
$result = ArrayUtil::moveValuesDown($array, $values); // returns [3, 4, 1, 2]
</code></pre>

<h3>moveValues</h3>
<h4>Description:</h4>
<p>Protected method that moves values either up or down within an array, based on the provided parameters.</p>

<h4>Signature:</h4>
<pre><code>protected static function moveValues(array $array, array $values, string $location): array</code></pre>

<h4>Parameters:</h4>
<ul>
    <li><code>$array</code> (array): The initial array.</li>
    <li><code>$values</code> (array): The values to be moved.</li>
    <li><code>$location</code> (string): The direction to move the values (<code>'up'</code> for up and <code>'down'</code> for down).</li>
</ul>

<h4>Returns:</h4>
<p><code>array</code>: The modified array with values moved.</p>

<h2>Conclusion</h2>
<p>The <code>ArrayUtil</code> class is a useful set of functions for array manipulation, offering functionalities such as checking for numeric keys and rearranging elements within an array. These methods are easy to use and can simplify many common array operations.</p>
