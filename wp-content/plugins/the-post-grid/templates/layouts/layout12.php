<?php
/**
 * Layout Template - 12
 *
 * @package RT_TPG
 */

use RT\ThePostGrid\Helpers\Fns;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

$html = $metaHtmltop = $metaHtmlbottom = $titleHtml = $catHtml = null;

if ( in_array( 'categories', $items ) && $categories ) {
	$catHtml .= "<span class='categories-links'>";

	if ( $catIcon ) {
		$catHtml .= "<i class='" . Fns::change_icon( 'fas fa-folder-open', 'folder' ) . "'></i>";
	}

	$catHtml .= "{$categories}</span>";
}

if ( in_array( 'post_date', $items ) && $date ) {
	$metaHtmltop .= "<span class='date-meta'>";

	if ( $metaIcon ) {
		$metaHtmltop .= "<i class='" . Fns::change_icon( 'far fa-calendar-alt', 'calendar' ) . "'></i>";
	}

	$metaHtmltop .= "{$date}</span>";
}

if ( in_array( 'author', $items ) ) {
	$metaHtmltop .= "<span class='author'>";

	if ( $metaIcon ) {
		$metaHtmltop .= "<i class='" . Fns::change_icon( 'fa fa-user', 'user' ) . "'></i>";
	}

	$metaHtmltop .= "{$author}</span>";
}

$metaHtmltop .= $catHtml;

if ( in_array( 'tags', $items ) && $tags ) {
	$metaHtmltop .= "<span class='post-tags-links'>";

	if ( $metaIcon ) {
		$metaHtmltop .= "<i class='" . Fns::change_icon( 'fa fa-tags', 'tag' ) . "'></i>";
	}

	$metaHtmltop .= "{$tags}</span>";
}

$num_comments = get_comments_number(); // get_comments_number returns only a numeric value

if ( in_array( 'comment_count', $items ) && $comment ) {
	$metaHtmltop .= '<span class="comment-count"><a href="' . get_comments_link() . '">';

	if ( $metaIcon ) {
		$metaHtmltop .= "<i class='" . Fns::change_icon( 'fas fa-comments', 'chat' ) . "'></i>";
	}

	$metaHtmltop .= $num_comments . '</a></span>';
}

if ( ! empty( $metaHtmltop ) && $metaPosition == 'above_title' ) {
	$titleHtml .= "<div class='post-meta-user {$metaSeparator}'>$metaHtmltop</div>";
}

if ( in_array( 'title', $items ) ) {
	$titleHtml .= sprintf( '<%1$s class="entry-title"><a data-id="%2$s" class="%3$s" href="%4$s"%5$s>%6$s</a></%1$s>', $title_tag, $pID, $anchorClass, $pLink, $link_target, $title );
}

$postMetaBottom = null;

if ( in_array( 'read_more', $items ) ) {
	$postMetaBottom .= "<span class='read-more'><a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>{$read_more_text}</a></span>";
}

$html .= sprintf( '<div class="%s" data-id="%d">', esc_attr( implode( ' ', [ $grid, $class ] ) ), $pID );
$html .= sprintf( '<div class="rt-holder%s">', $tpg_title_position ? ' rt-with-title-' . $tpg_title_position : null );

if ( $tpg_title_position == 'above' ) {
	$html .= sprintf( '<div class="rt-detail rt-with-title">%s</div>', $titleHtml );
}

if ( $imgSrc ) {
	$html .= '<div class="rt-img-holder">';
	$html .= "<a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>{$imgSrc}</a>";
	$html .= '</div> ';
}

$html .= "<div class='rt-detail'>";

if ( $tpg_title_position == 'below' ) {
	$html .= $titleHtml;
}

if ( ! empty( $metaHtmltop ) && ( empty( $metaPosition ) || $metaPosition == 'above_excerpt' ) ) {
	$html .= "<div class='post-meta-user {$metaSeparator}'>$metaHtmltop</div>";
}

if ( ! $tpg_title_position ) {
	$html .= $titleHtml;
}

if ( in_array( 'excerpt', $items ) ) {
	$html .= "<div class='tpg-excerpt'>{$excerpt}</div>";
}

if ( ! empty( $metaHtmltop ) && $metaPosition == 'below_excerpt' ) {
	$html .= "<div class='post-meta-user {$metaSeparator}'>$metaHtmltop</div>";
}

if ( ! empty( $postMetaBottom ) ) {
	$html .= "<div class='post-meta {$btn_alignment_class}'>$postMetaBottom</div>";
}

$html .= '</div>';
$html .= '</div>';
$html .= '</div>';

Fns::print_html( $html );
