
<h1>Assemble the result of a database query using Php and Mysql.</h1>
<p>Assembly is done using a structure that can be of the form: "table1.table2*|junction". The entities from table 2 will be added to the entity from table 1 in the field named "table2". If it is desired for these entities (from table 2) to be added into a different field, then a new location can be added as follows: 'table1.table2*(location)'.
</p>
<p>
	The * character indicates that there are multiple entities from table 2. These entities will be added to the entity from table 1 in an array with entities. Without the * character, only a single entity from table 2 will be added to the entity from table 1 and not an array with entities.
</p>
<p>
	If a junction is used in the union between table 1 and table 2, then the junction table name will be added as follows: 'table1.table2*(location)|junction'
</p>
<p>
	Multiple structures can be added: ['user.salaries(salary)', 'user.photos(avatar)']
</p>
<p>
	<a href="https://github.com/updatablejs/ujb/blob/main/docs/database/assembler.html">More information can be found here</a>
</p>

<p>Here is an example.</p>
<pre>
$result = $database->prepare(
		'SELECT * FROM albums 
			LEFT JOIN photos ON albums.album_id = photos.album_id'
	)
	
	->setAssembler([
		'structure' => 'albums.photos*',
	])

	->execute();

print_r($result);
</pre>

<pre>
Array
(
    [0] => Array
        (
            [album_id] => 1
            [name] => The Wild Places
            [date_created] => 2024-03-01
            [photos] => Array
                (
                    [0] => Array
                        (
                            [photo_id] => 1
                            [album_id] => 1
                            [file] => the_wild_places_1.jpeg
                        )

                    [1] => Array
                        (
                            [photo_id] => 2
                            [album_id] => 1
                            [file] => the_wild_places_2.jpeg
                        )
                )

        )

    [1] => Array
        (
            [album_id] => 2
            [name] => Wherever We Go
            [date_created] => 2024-03-02
            [photos] => Array
                (
                    [0] => Array
                        (
                            [photo_id] => 4
                            [album_id] => 2
                            [file] => wherever_we_go_1.jpeg
                        )
                )

        )

    [3] => Array
        (
            [album_id] => 4
            [name] => See the World
            [date_created] => 2024-03-01
            [photos] => Array
                (
                )

        )
)
</pre>

<h2>Documentation</h2>
 <ul>
  <li><a href="https://github.com/updatablejs/ujb/blob/main/docs/database/assembler.html">Assembler</a></li>
</ul>
