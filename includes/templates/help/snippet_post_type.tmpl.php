<p>This is where you create the Snippet that will get displayed on your pages/posts. It's a Custom Post Type, so the basic functionality is that of a page.</p>
<ul>
	<li><strong>Snippet Groups:</strong> Like categories, their purpose is to tell the plugin which set of Snippets to evaluate when processing the shortcode.
	Ideally, you will place shortcodes with different groups in different parts of your page/post. This way you can control what gets shown where.</li>
	<li><strong>Published vs. Draft:</strong> Only published Snippets get picked from a given group.</li>
	<li><strong>Shortcodes in Snippets:</strong> Shortcodes are allowed, but make sure to to NOT create an infinite loop by adding a Cronblocks shortcode for the same group containing THIS Snippet.</li>
	<li><strong>Snippet Controls:</strong> This is the heart of Cronblocks. It controls when the Snippet will be shown using <strong>either</strong> a geo-location <strong>or</strong> a date/time.</li>
	<li><strong>Priority:</strong> For Snippets that overlap (say one Snippet is to be shown to US visitors, and another to any country), use Priorities to decide which one to display.
	If more than one Snippet matches the geo-location/timestamp and have the same Priority, one will be picked randomly to be displayed</li>
</ul>