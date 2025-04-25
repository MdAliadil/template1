<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Schema::defaultStringLength(191);

        try {
            view()->composer('*', function ($view){
                $mydata['links']             = \App\Model\Link::get();
                $mydata['sessionOut']        = \App\Model\PortalSetting::where('code', 'sessionout')->first()->value;
                $mydata['complaintsubject']  = \App\Model\Complaintsubject::get();
                $mydata['topheadcolor']      = \App\Model\PortalSetting::where('code', "topheadcolor")->first();
                $mydata['sidebarlightcolor'] = \App\Model\PortalSetting::where('code', "sidebarlightcolor")->first();
                $mydata['sidebardarkcolor']  = \App\Model\PortalSetting::where('code', "sidebardarkcolor")->first();
                $mydata['sidebariconcolor']  = \App\Model\PortalSetting::where('code', "sidebariconcolor")->first();
                $mydata['sidebarchildhrefcolor'] = \App\Model\PortalSetting::where('code', "sidebarchildhrefcolor")->first();
                $mydata['schememanager'] = \App\Model\PortalSetting::where('code', "schememanager")->first();

                $mydata['company'] = \App\Model\Company::where('website', $_SERVER['HTTP_HOST'])->first();

                if($mydata['company']){
                    $news = \App\Model\Companydata::where('company_id', $mydata['company']->id)->first();
                }else{
                    $news = null;
                }

                if($news){
                    $mydata['news'] = $news->news;
                    $mydata['notice'] = $news->notice;
                    $mydata['billnotice'] = $news->billnotice;
                    $mydata['supportnumber'] = $news->number;
                    $mydata['supportemail'] = $news->email;
                }else{
                    $mydata['news'] = "";
                    $mydata['notice'] = "";
                    $mydata['billnotice'] = "";
                    $mydata['supportnumber'] = "";
                    $mydata['supportemail'] = "";
                }

                $view->with('mydata', $mydata);    
            }); 
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}