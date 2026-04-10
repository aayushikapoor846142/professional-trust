<?php 


use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
 
// Home 
Breadcrumbs::for('home', function (BreadcrumbTrail $trail): void {
    $trail->push('Home', url('/'));
});

// Home > Report Unauthorized Professional
Breadcrumbs::for('report-unauthorized-professional', function (BreadcrumbTrail $trail): void {
    $trail->parent('home');
      $trail->push("Report Unauthorized Professional", url('report-unauthorized-professional'));
  
  });

 // Home > Report Company
Breadcrumbs::for('report.company', function (BreadcrumbTrail $trail): void {
  $trail->parent('home');
  $trail->push("Report Company", url('report/company'));
});
Breadcrumbs::for('articles-list', function (BreadcrumbTrail $trail): void {
  $trail->parent('home');
  $trail->push("Articles", url('articles'));
});
Breadcrumbs::for('articles-detail', function ($trail, $record) {
    $trail->parent('articles-list');
    $trail->push($record->name, route('articles.detail', $record->slug));
});

Breadcrumbs::for('page', function (BreadcrumbTrail $trail, $get_page): void {
  $trail->parent('home');
  $trail->push($get_page->name, url('page/'.$get_page->slug));
});

// 
Breadcrumbs::for('guides-list', function (BreadcrumbTrail $trail): void {
  $trail->parent('home');
  $trail->push("Guides", url('guides'));
});
Breadcrumbs::for('faqs-list', function (BreadcrumbTrail $trail): void {
  $trail->parent('home');
  $trail->push("FAQ", url('faqs'));
});
Breadcrumbs::for('faqs-detail', function ($trail, $record) {
  $trail->parent('faqs-list');
  $trail->push($record->title, route('faqs.detail', ['id' => $record->id, 'slug' => Str::slug($record->title, '-')]));
});
Breadcrumbs::for('guides-detail', function ($trail, $record) {
    $trail->parent('guides-list');
    $trail->push($record->name, route('guides.detail', $record->slug));
});
Breadcrumbs::for('knowledgebase', function (BreadcrumbTrail $trail): void {
  $trail->parent('home');
  $trail->push("Knowledge Base", url('knowledgebase/all'));
});
// Home > Report Professional
Breadcrumbs::for('report.individual', function (BreadcrumbTrail $trail): void {
  $trail->parent('home');
  $trail->push("Report Professional", url('report/professional'));
});

// Home > Quick Tip-Offs
Breadcrumbs::for('quick-tip-offs', function (BreadcrumbTrail $trail): void {
  $trail->parent('home');
  $trail->push("Quick Tip-Offs", url('quick-tip-offs'));
});

 // Home > Report Social Media Contet
 Breadcrumbs::for('report.social-media', function (BreadcrumbTrail $trail): void {
  $trail->parent('home');
  $trail->push("Report Social Media Content", url('report/social-media-content'));
});






