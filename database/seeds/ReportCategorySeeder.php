<?php

use App\Enums\ReportCategoryType;
use Illuminate\Database\Seeder;
use App\Models\ReportCategory;

class ReportCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ReportCategory::query()->delete();
        $report_categories = ['Harassment','Inappropriate Behavior','Spamming','Other'];

        foreach($report_categories as $category){
            ReportCategory::firstOrCreate(['label' => $category]);
        }

        $profileReports = collect([
            'Pretending to Be Someone',
            'Fake Account',
            'Fake Name',
            'Posting Inappropriate Things',
            'Harassment or Bullying'
        ]);

        $profileReports->each(function ($category) {
            ReportCategory::firstOrCreate([
                'label' => $category, 'type' => ReportCategoryType::USER
            ]);
        });

        $itemReports = collect([
            'Inaccurate Description',
            'Promoting a business',
            'Animal Sales',
            'No Intent to Sell',
            'Weapon or Drug Sales',
            'Sexualized Content or Adult Products',
            'Descriminatory Listing',
            'Abusive or Harmful Content',
            'Scam',
            'Appears to be Counterfeit',
            'Child Abuse',
        ]);

        $itemReports->each(function ($category) {
            ReportCategory::firstOrCreate([
                'label' => $category, 'type' => ReportCategoryType::ITEM
            ]);
        });

        $recommendationReports = collect([
            'False News',
            'Spam',
            'Harassment',
            'Hate Speech',
            'Nudity or Sexual Activity',
            'Violence',
        ]);

        $recommendationReports->each(function ($category) {
            ReportCategory::firstOrCreate([
                'label' => $category, 'type' => ReportCategoryType::RECOMMENDATIONS
            ]);
        });
    }
}
