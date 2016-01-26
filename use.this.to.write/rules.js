RULES = { // you can create your own parsing fules all are processed in the after()
	head: {
		'# ' : [ '<h1>', '</h1>'],
	},
	onepart: { // each pair of lines is start-end lines of the block (toggle ignore ON-OFF in each pair) 
		'`START': [ '<code>', true],
		'`END': [ '</code>', false]
	},
	twoparts: {
		'%%': [ '<a href="REPLACEME">REPLACEME</a>'],
		'`': [ '<code>', '</code>'],
		'$': [ '<pass>', '</pass>'],
		'***' : [ '<s2>', '</s2>'],
		'**' : [ '<s1>', '</s1>'],
		'*' : [ '<b>', '</b>'],
	},
	threeparts: { // no tags, just a parsing format  -- note! keys should not distinct! to avoid conflicts
		'%%%': '<a href="REPLACEME2">REPLACEME1</a>'
	}
	
}