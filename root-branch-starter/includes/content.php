<?php
/**
 * Starter content data, taken from the current rootandbranchisrael.com site.
 *
 * @package RootBranchStarter
 */

defined( 'ABSPATH' ) || exit;

return array(

	/*
	 * Photographs: file in /media => alt text and the pattern keys it fills.
	 */
	'media'    => array(
		'hero-olive-tree'          => array( 'An ancient olive tree in Israel', array( 'hero' ) ),
		'statement-olive-tree'      => array( 'Ancient olive trees in an Israeli grove', array( 'statement-left' ) ),
		'hero-olive-harvest'        => array( 'Olive groves and the harvest in Israel', array( 'hero-video' ) ),
		'hero-olive-harvest-poster' => array( 'Olive groves in Israel', array( 'hero-video-poster' ) ),
		'value-unity'              => array( 'Volunteers harvesting olives onto blue tarps', array( 'value-unity' ) ),
		'value-connection'         => array( 'Root & Branch volunteers celebrating together', array( 'value-connection' ) ),
		'value-experience'         => array( 'Volunteers with crates of harvested olives', array( 'value-experience' ) ),
		'value-partnership-olives' => array( 'A crate of freshly harvested olives', array( 'value-partnership', 'statement-right' ) ),
		'founders'                 => array( 'Marziyeh Amirizadeh and Jonathan Feldstein, founders of Root & Branch', array( 'founders' ) ),
		'marziyeh-amirizadeh'      => array( 'Marziyeh Amirizadeh', array( 'founder-marziyeh' ) ),
		'jonathan-feldstein'       => array( 'Jonathan Feldstein', array( 'founder-jonathan' ) ),
		'historical-achievement'   => array( 'Root & Branch volunteers at an ancient site in Israel', array( 'history' ) ),
		'root-and-branch-logo'     => array( 'Root & Branch', array( 'logo' ) ),
		'new-persia-logo'          => array( 'New Persia — Jeremiah 49:38-39', array( 'logo-new-persia' ) ),
		'genesis-123-logo'         => array( 'Genesis 123 Foundation', array( 'logo-genesis-123' ) ),
		'atlanta-event'            => array( 'Root & Branch Unity Conference Atlanta', array( 'event-atlanta' ) ),
		'trip-1'                   => array( 'Root and Branch group walking through the historic alleyways of Jerusalem', array( 'trip-1' ) ),
		'trip-2'                   => array( 'Root and Branch participants touring ancient ruins in Israel', array( 'trip-2' ) ),
		'trip-3'                   => array( 'Root and Branch group at a historic site in Jerusalem', array( 'trip-3' ) ),
	),

	/*
	 * Gallery photographs supplied for the site, in order. The first twelve
	 * fill the home collage; six more illustrate the About timeline.
	 */
	'gallery'  => array( '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24' ),
	'timeline' => array( '13', '14', '15', '16', '18', '19' ),

	/*
	 * Media coverage. Years are not shown on the current site; dates only
	 * set the order cards appear in.
	 */
	'articles' => array(
		array( 'The Olive Tree\'s Trunk: Roots and Branches in a New Dimension of Building Bridges', 'https://cbnisrael.org/2025/08/14/the-olive-trees-trunk-roots-and-branches-a-new-dimension-of-building-bridges/', '2025-08-14', 'cbnisrael-2025-08-14', 'Root & Branch olive harvest along Gaza border' ),
		array( 'Tu Bishvat and the Blossoming of Olive Trees in Israel', 'https://www.jpost.com/christianworld/article-884981', '2025-02-13', 'jpost-884981', 'Olive trees blossoming in Israel' ),
		array( 'Root and Branch: The New Olive Tree Dream Team', 'https://cbnisrael.org/2025/01/02/root-branch-the-new-olive-tree-dream-team/', '2025-01-02', 'cbnisrael-2025-01-02', 'Root & Branch volunteer team at olive harvest' ),
		array( 'Christian Leaders from Iran to Alaska are Joining Root & Branch', 'https://israel365news.com/397887/christian-leaders-from-iran-to-alaska-are-joining-root-branch/', '2024-11-15', 'gallery-07', 'Volunteers harvesting olives in Israel' ),
		array( 'Come and Harvest Olives! Jonathan Feldstein Talks About \'Root & Branch\' Project', 'https://allisrael.com/come-and-harvest-olives-jonathan-feldstein-talks-about-root-and-branch-project', '2024-10-15', 'gallery-09', 'Group of volunteers during the olive harvest' ),
		array( 'Iranian Olive Branch: Evangelist Teams Up with Orthodox Jew to Help Reap the Harvest', 'https://www.israeltoday.co.il/read/iranian-olive-branch-2/', '2024-09-05', 'avi-jon-marzi', 'Marzi, Jonathan, Avi at branch olive harvest event' ),
		array( 'Christians and Jews Together in a Powerful Agricultural Pilgrimage', 'https://www.crosswalk.com/headlines/contributors/guest-commentary/root-and-branch-brings-christians-and-jews-together-to-help-restore-israels-land.html', '2024-05-13', 'marzi-jonathan', 'Marzi and Jonathan at root and branch event' ),
	),

	/*
	 * Videos: [ title, video file or external URL, date, thumbnail, alt, is external ].
	 */
	'videos'   => array(
		array( 'From Prison to the Holy Land, the Amazing story of an Iranian Woman', 'https://afr.net/podcasts/the-middle-east-report/2026/january/from-prison-to-the-holy-land-the-amazing-story-of-an-iranian-woman/', '2026-01-21', 'mer-800x500', 'The Middle East Report podcast cover', true ),
		array( 'Jake Smith Radio Show 2025', 'https://storage.googleapis.com/msgsndr/bHDNwcen6apPGom1BhVf/media/68145e20eba7a3dce0188c44.mp4', '2025-04-28', 'jake-smith-radio-show', 'Jake Smith Radio Show 2025', false ),
		array( 'Minister of Agriculture, Avi Dichter Words of Encouragement', 'https://storage.googleapis.com/msgsndr/bHDNwcen6apPGom1BhVf/media/6806bcc79146b56d915afa98.mp4', '2025-04-18', 'minister-of-agriculture', 'Minister of Agriculture', false ),
		array( 'Atlanta Live Interview', 'https://storage.googleapis.com/msgsndr/bHDNwcen6apPGom1BhVf/media/67d9bc8c652cf1a68c85c748.mp4', '2025-03-16', 'atlanta-interview', 'Atlanta Live Interview', false ),
		array( 'Club 36, Watchmen Broadcasting Interview', 'https://storage.googleapis.com/msgsndr/bHDNwcen6apPGom1BhVf/media/67d9bc8822c2336c8b5d8643.mp4', '2025-03-15', 'club36', 'Club 36, Watchmen Broadcasting Interview', false ),
		array( 'Why Join Root & Branch Harvesting Olives and Blessing Israel', 'https://storage.googleapis.com/msgsndr/iIOxPilMJecpgwHQIho9/media/67b4fde9dbb71761cd6b1b21.mp4', '2025-02-18', 'full-story', 'Group of volunteers during the olive harvest', false ),
		array( 'Root & Branch Project Unites Communities', 'https://storage.googleapis.com/msgsndr/iIOxPilMJecpgwHQIho9/media/67b67f31145bb78d022d64ed.mp4', '2024-12-20', 'cbn-news-gaza-rb', 'Volunteers harvesting olives with long poles and blue tarps', false ),
		array( 'Healing at Gaza Border: Root & Branch\'s Olive Story', 'https://storage.googleapis.com/msgsndr/iIOxPilMJecpgwHQIho9/media/67b67a4b88b2f765083c9b03.mp4', '2024-12-10', 'healing-at-gaza-border', 'Volunteer wearing Root & Branch olive tree logo sweatshirt', false ),
		array( 'Root & Branch Olive Harvest Project', 'https://storage.googleapis.com/msgsndr/iIOxPilMJecpgwHQIho9/media/67b67dd583fcbc99bb55c747.mp4', '2024-08-06', 'gallery-04', 'Scene from the olive harvest project', false ),
	),
);
