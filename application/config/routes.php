<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['migrate'] = 'migrate';
$route['company/check-uen'] = 'company/checkUEN';
$route['company/company-lists'] = 'company/companyList';
$route['company/new-application'] = 'company/newApplication';
$route['company/update-application'] = 'company/updateApplication';
$route['company/get-application'] = 'company/getApplication';
$route['company/get-categories'] = 'company/getCategories';
$route['company/get-companies'] = 'company/getCompanies';
$route['company/get-courses'] = 'company/getCourses';
$route['company/get-salespersons'] = 'company/getSalespersons';
$route['company/get-statuses'] = 'company/getStatuses';
$route['company/filter-applications'] = 'company/filterApplications';

$route['company/payment-terms'] = 'company/paymentTerms';
$route['company/company-lists-filter'] = 'company/companyListFilter';
$route['company/company-lists-name'] = 'company/companyListName';
$route['company/add-learner'] = 'company/addLearner';
$route['company/add-company'] = 'company/addCompany';
$route['company/update-company'] = 'company/updateCompany';
$route['company/add-learner-manager'] = 'company/addLearnerManager';
$route['company/get-company-uen'] = 'company/getCompanyUEN';
$route['company/get-company-name'] = 'company/getCompanyName';
$route['company/deactivate-company'] = 'company/deactivateCompany';
$route['company/view-learners'] = 'company/viewLearners';
$route['company/view-learner-managers'] = 'company/viewLearnerManagers';
$route['company/deactivate-learner'] = 'company/deactivateLearner';
$route['company/deactivate-learner-manager'] = 'company/deactivateLearnerManager';

$route['learners/get-learners'] = 'learners/getLearners';
$route['learners/get-learner'] = 'learners/getLearner';
$route['learners/get-learner-doc'] = 'learners/getLearnerDoc';
$route['learners/update-learner'] = 'learners/updateLearner';
$route['learners/search-learner'] = 'learners/searchLearner';
$route['company/get-company-learners'] = 'company/getCompanyLearners';
$route['company/latest-application'] = 'company/latestApplication';
$route['company/get-expiring-core-trades'] = 'company/getExpiringCoreTrades';
$route['company/change-account-status'] = 'company/changeAccountStatus';
$route['company/get-unpaid-invoices'] = 'company/getUnpaidInvoices';