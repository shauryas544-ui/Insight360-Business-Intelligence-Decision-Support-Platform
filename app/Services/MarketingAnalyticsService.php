<?php

namespace App\Services;

use App\Helpers\Database;

class MarketingAnalyticsService
{
    public function getMarketingAnalytics(): array
    {
        $query = "
            SELECT
                c.id,
                c.name,
                c.channel,
                c.budget,
                c.start_date,
                c.end_date,
                c.status,
                COALESCE(cr.impressions, 0) as impressions,
                COALESCE(cr.clicks, 0) as clicks,
                COALESCE(cr.leads, 0) as leads,
                COALESCE(cr.conversions, 0) as conversions,
                COALESCE(cr.revenue_generated, 0.0) as revenue
            FROM campaigns c
            LEFT JOIN campaign_results cr ON c.id = cr.campaign_id
            ORDER BY c.start_date DESC
        ";
        $campaigns = Database::fetchAll($query);

        $totalBudget = 0.0;
        $totalRevenue = 0.0;
        $totalConversions = 0;
        $totalLeads = 0;
        $totalClicks = 0;
        $totalImpressions = 0;

        foreach ($campaigns as &$camp) {
            $b = (float) $camp['budget'];
            $r = (float) $camp['revenue'];
            $conv = (int) $camp['conversions'];
            $leads = (int) $camp['leads'];
            $clicks = (int) $camp['clicks'];
            $impr = (int) $camp['impressions'];

            $camp['ctr'] = $impr > 0 ? round(($clicks / $impr) * 100.0, 2) : 0.0;
            $camp['conversion_rate'] = $leads > 0 ? round(($conv / $leads) * 100.0, 2) : 0.0;
            $camp['cac'] = $conv > 0 ? round($b / $conv, 2) : 0.0;
            $camp['roi'] = $b > 0 ? round((($r - $b) / $b) * 100.0, 2) : 0.0;

            $totalBudget += $b;
            $totalRevenue += $r;
            $totalConversions += $conv;
            $totalLeads += $leads;
            $totalClicks += $clicks;
            $totalImpressions += $impr;
        }

        $overallRoi = $totalBudget > 0 ? round((($totalRevenue - $totalBudget) / $totalBudget) * 100.0, 2) : 0.0;
        $overallConvRate = $totalLeads > 0 ? round(($totalConversions / $totalLeads) * 100.0, 2) : 0.0;
        $overallCac = $totalConversions > 0 ? round($totalBudget / $totalConversions, 2) : 0.0;

        return [
            'campaigns' => $campaigns,
            'total_budget' => $totalBudget,
            'total_revenue' => $totalRevenue,
            'total_conversions' => $totalConversions,
            'overall_roi' => $overallRoi,
            'overall_cac' => $overallCac,
            'overall_conv_rate' => $overallConvRate
        ];
    }
}

