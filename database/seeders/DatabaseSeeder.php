<?php

namespace Database\Seeders;

use App\Helpers\Database;

class DatabaseSeeder
{
    public function run(): void
    {
        echo "Starting InsightFlow Database Seeding...\n";

        $this->cleanTables();
        $this->seedRolesAndPermissions();
        $this->seedUsers();
        $this->seedCategories();
        $this->seedRegions();
        $this->seedSalespersons();
        $this->seedProducts();
        $this->seedCustomers();
        $this->seedOrdersAndItems();
        $this->seedCampaigns();
        $this->seedTargets();

        echo "[OK] Database seeding completed successfully!\n";
    }

    private function cleanTables(): void
    {
        echo "-> Resetting existing records...\n";
        $driver = Database::getDriver();

        if ($driver === 'sqlite') {
            Database::execute("PRAGMA foreign_keys = OFF;");
        } else {
            Database::execute("SET FOREIGN_KEY_CHECKS = 0;");
        }

        $tables = [
            'order_items', 'returns', 'inventory', 'orders', 'customers',
            'salespersons', 'products', 'categories', 'campaign_results',
            'campaigns', 'targets', 'role_permissions', 'users', 'roles',
            'customer_segments', 'customer_cohorts', 'product_metrics',
            'regional_metrics', 'monthly_metrics', 'forecast_results',
            'anomalies', 'alerts', 'recommendations', 'data_quality_logs', 'data_imports'
        ];

        foreach ($tables as $tbl) {
            Database::execute("DELETE FROM {$tbl};");
        }

        if ($driver === 'sqlite') {
            try {
                Database::execute("DELETE FROM sqlite_sequence;");
            } catch (\Throwable $e) {}
            Database::execute("PRAGMA foreign_keys = ON;");
        } else {
            Database::execute("SET FOREIGN_KEY_CHECKS = 1;");
        }
    }

    private function seedRolesAndPermissions(): void
    {
        echo "-> Seeding Roles & Permissions...\n";

        $roles = [
            ['name' => 'admin', 'display_name' => 'System Administrator', 'description' => 'Full administrative access to datasets, users, system settings, and analytics.'],
            ['name' => 'analyst', 'display_name' => 'Senior BI Analyst', 'description' => 'Deep analytics access: RFM, cohorts, pareto, forecasting, anomalies, data explorer.'],
            ['name' => 'manager', 'display_name' => 'Business Executive / Manager', 'description' => 'Executive dashboards, KPI monitoring, alerts, recommendations, and reports.']
        ];

        foreach ($roles as $r) {
            Database::execute(
                "INSERT INTO roles (name, display_name, description) VALUES (?, ?, ?)",
                [$r['name'], $r['display_name'], $r['description']]
            );
        }
    }

    private function seedUsers(): void
    {
        echo "-> Seeding Users...\n";

        $rolesList = Database::fetchAll("SELECT id, name FROM roles");
        $rolesMap = [];
        foreach ($rolesList as $rl) {
            $rolesMap[$rl['name']] = (int) $rl['id'];
        }

        $users = [
            [
                'name' => 'Alex Morgan (Admin)',
                'email' => 'admin@insightflow.io',
                'password' => 'AdminPassword123!',
                'role_id' => $rolesMap['admin'] ?? 1
            ],
            [
                'name' => 'Elena Rostova (Analyst)',
                'email' => 'analyst@insightflow.io',
                'password' => 'AnalystPassword123!',
                'role_id' => $rolesMap['analyst'] ?? 2
            ],
            [
                'name' => 'Marcus Vance (Manager)',
                'email' => 'manager@insightflow.io',
                'password' => 'ManagerPassword123!',
                'role_id' => $rolesMap['manager'] ?? 3
            ]
        ];

        foreach ($users as $u) {
            $hash = password_hash($u['password'], PASSWORD_BCRYPT);
            Database::execute(
                "INSERT INTO users (name, email, password_hash, role_id, status) VALUES (?, ?, ?, ?, 'active')",
                [$u['name'], $u['email'], $hash, $u['role_id']]
            );
        }
    }

    private function seedCategories(): void
    {
        echo "-> Seeding Categories...\n";

        $cats = [
            ['Cloud Infrastructure', 'CLOUD', 'Servers, Virtual Machines, and Scalable Cloud Hosting solutions.'],
            ['Data & Analytics Software', 'ANALYTICS', 'Enterprise Data Warehousing, BI tools, and ETL pipelines.'],
            ['Cybersecurity & Compliance', 'SECURITY', 'Endpoint protection, firewalls, threat detection, and compliance engines.'],
            ['AI & Machine Learning Suite', 'AI_ML', 'Pre-trained models, LLM APIs, and intelligent automation platforms.'],
            ['Developer Tools & DevOps', 'DEVOPS', 'CI/CD pipelines, container orchestration, and IDE telemetry.'],
            ['Enterprise Collaboration', 'COLLAB', 'Secure messaging, workflow automation, and productivity suites.'],
            ['Hardware & Edge Appliances', 'HARDWARE', 'Rack servers, edge IoT gateways, and storage area networks.'],
            ['Consulting & Professional Services', 'SERVICES', 'Implementation, migration, and 24/7 mission-critical SLA support.']
        ];

        foreach ($cats as $c) {
            Database::execute("INSERT INTO categories (name, code, description) VALUES (?, ?, ?)", $c);
        }
    }

    private function seedRegions(): void
    {
        echo "-> Seeding Regions...\n";

        $regions = [
            ['North America - Northeast', 'United States', 'New York', 'New York City', 'US-NE'],
            ['North America - West Coast', 'United States', 'California', 'San Francisco', 'US-WC'],
            ['North America - Midwest', 'United States', 'Illinois', 'Chicago', 'US-MW'],
            ['North America - Southeast', 'United States', 'Georgia', 'Atlanta', 'US-SE'],
            ['North America - Southwest', 'United States', 'Texas', 'Austin', 'US-SW'],
            ['North America - Northwest', 'United States', 'Washington', 'Seattle', 'US-NW'],
            ['Europe - UK & Ireland', 'United Kingdom', 'Greater London', 'London', 'EU-UK'],
            ['Europe - Central DACH', 'Germany', 'Bavaria', 'Munich', 'EU-DE'],
            ['Asia Pacific - East', 'Japan', 'Kanto', 'Tokyo', 'AP-JP'],
            ['Asia Pacific - South', 'Singapore', 'Central Region', 'Singapore', 'AP-SG']
        ];

        foreach ($regions as $r) {
            Database::execute("INSERT INTO regions (name, country, state, city, code) VALUES (?, ?, ?, ?, ?)", $r);
        }
    }

    private function seedSalespersons(): void
    {
        echo "-> Seeding Salespersons...\n";

        $sales = [
            ['David Chen', 'david.chen@insightflow.io', 1, 350000.00],
            ['Sarah Jenkins', 'sarah.jenkins@insightflow.io', 2, 450000.00],
            ['Michael Miller', 'michael.miller@insightflow.io', 3, 220000.00], // Midwest rep
            ['Jessica Taylor', 'jessica.taylor@insightflow.io', 4, 300000.00],
            ['Robert Hernandez', 'robert.hernandez@insightflow.io', 5, 380000.00],
            ['Amanda Vance', 'amanda.vance@insightflow.io', 6, 320000.00],
            ['Oliver Smith', 'oliver.smith@insightflow.io', 7, 400000.00],
            ['Hannah Becker', 'hannah.becker@insightflow.io', 8, 360000.00],
            ['Kenji Sato', 'kenji.sato@insightflow.io', 9, 310000.00],
            ['Priya Sharma', 'priya.sharma@insightflow.io', 10, 280000.00]
        ];

        foreach ($sales as $s) {
            Database::execute(
                "INSERT INTO salespersons (name, email, region_id, target_quarterly) VALUES (?, ?, ?, ?)",
                $s
            );
        }
    }

    private function seedProducts(): void
    {
        echo "-> Seeding Products & Inventory...\n";

        $products = [
            // Cloud Infrastructure (cat 1)
            ['SKU-CLD-01', 'Cloud Cluster Enterprise 64C', 1, 1200.00, 2400.00],
            ['SKU-CLD-02', 'Cloud Storage Tier 1 (10TB)', 1, 300.00, 650.00],
            ['SKU-CLD-03', 'Serverless Compute Gateway', 1, 450.00, 1100.00],
            ['SKU-CLD-04', 'Kubernetes Managed Grid', 1, 850.00, 1950.00],
            ['SKU-CLD-05', 'Hybrid Cloud Edge Node (High Volume, Low Margin)', 1, 1400.00, 1550.00], // Scenario: Product Issue

            // Data & Analytics (cat 2)
            ['SKU-DAT-01', 'Data Lakehouse Enterprise License', 2, 800.00, 2800.00],
            ['SKU-DAT-02', 'Realtime Streaming Bus (Kafka)', 2, 500.00, 1450.00],
            ['SKU-DAT-03', 'BI Analytics Visualization Seat', 2, 120.00, 480.00],
            ['SKU-DAT-04', 'Automated ETL Pipeline Suite', 2, 350.00, 1200.00],

            // Cybersecurity (cat 3)
            ['SKU-SEC-01', 'Zero-Trust Network Access Engine', 3, 400.00, 1600.00],
            ['SKU-SEC-02', 'NextGen AI Firewall Gateway', 3, 900.00, 2200.00],
            ['SKU-SEC-03', 'Continuous Vulnerability Scanner', 3, 250.00, 850.00],
            ['SKU-SEC-04', 'SOC SIEM Log Orchestrator', 3, 600.00, 2100.00],

            // AI & ML (cat 4)
            ['SKU-AIM-01', 'Enterprise LLM Dedicated Fine-tuning Engine', 4, 1500.00, 4800.00],
            ['SKU-AIM-02', 'Vector Database VectorStore Ultra', 4, 300.00, 1250.00],
            ['SKU-AIM-03', 'Autonomous Agent Runtime Hub', 4, 450.00, 1800.00],
            ['SKU-AIM-04', 'Computer Vision Edge Model', 4, 600.00, 1950.00],

            // DevOps (cat 5)
            ['SKU-DEV-01', 'DevSecOps Pipeline Orchestrator', 5, 200.00, 750.00],
            ['SKU-DEV-02', 'Infrastructure-as-Code Telemetry', 5, 150.00, 520.00],
            ['SKU-DEV-03', 'GitOps Automated Deployment Suite', 5, 280.00, 950.00],

            // Enterprise Collaboration (cat 6)
            ['SKU-COL-01', 'Unified Workspace Enterprise 50-Seat', 6, 250.00, 1000.00],
            ['SKU-COL-02', 'Knowledge Management AI Wiki', 6, 180.00, 680.00],
            ['SKU-COL-03', 'Secure Compliance Archiving Hub', 6, 320.00, 1150.00],

            // Hardware & Edge (cat 7)
            ['SKU-HRD-01', '1U Rack Supercomputing Appliance', 7, 2800.00, 4900.00],
            ['SKU-HRD-02', 'Ruggedized IoT Edge Controller', 7, 450.00, 1200.00],
            ['SKU-HRD-03', 'High-Throughput NVMe SAN Array', 7, 3400.00, 6200.00],

            // Services (cat 8)
            ['SKU-SRV-01', 'Cloud Migration Jumpstart Package', 8, 1200.00, 3500.00],
            ['SKU-SRV-02', '24/7 Dedicated SRE Support Tier', 8, 800.00, 2400.00],
            ['SKU-SRV-03', 'AI Transformation Strategy Audit', 8, 1500.00, 5000.00]
        ];

        foreach ($products as $p) {
            Database::execute(
                "INSERT INTO products (sku, product_name, category_id, cost_price, selling_price, active)
                 VALUES (?, ?, ?, ?, ?, 1)",
                $p
            );
            $prodId = Database::lastInsertId();
            Database::execute(
                "INSERT INTO inventory (product_id, current_stock, safety_stock, reorder_level)
                 VALUES (?, ?, ?, ?)",
                [$prodId, rand(40, 250), 30, 60]
            );
        }
    }

    private function seedCustomers(): void
    {
        echo "-> Seeding Customers...\n";

        $firstNames = ['Apex', 'Vanguard', 'Omni', 'Quantum', 'Nexus', 'Starlight', 'Hyperion', 'Crestview', 'Solaria', 'Titan', 'Acro', 'Zenith', 'Synergy', 'Orion', 'Pinnacle', 'Summit', 'Global', 'BlueWave', 'DataCore', 'CyberPulse'];
        $lastNames = ['Dynamics', 'Technologies', 'Systems', 'Logistics', 'Financial', 'Healthcare', 'Energy', 'Capital', 'Labs', 'Solutions', 'Holdings', 'Industries', 'Enterprises', 'Ventures', 'Networks'];

        $cities = [
            ['New York', 'NY', 1],
            ['Boston', 'MA', 1],
            ['San Francisco', 'CA', 2],
            ['Los Angeles', 'CA', 2],
            ['Chicago', 'IL', 3], // Midwest
            ['Indianapolis', 'IN', 3],
            ['Atlanta', 'GA', 4],
            ['Miami', 'FL', 4],
            ['Austin', 'TX', 5],
            ['Dallas', 'TX', 5],
            ['Seattle', 'WA', 6],
            ['Portland', 'OR', 6],
            ['London', 'UK', 7],
            ['Manchester', 'UK', 7],
            ['Munich', 'DE', 8],
            ['Frankfurt', 'DE', 8],
            ['Tokyo', 'JP', 9],
            ['Singapore', 'SG', 10]
        ];

        $codeCounter = 1000;
        for ($i = 0; $i < 220; $i++) {
            $company = $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)] . ' ' . ($i > 50 ? ($i % 3 === 0 ? 'LLC' : 'Inc') : 'Corp');
            $loc = $cities[array_rand($cities)];
            $code = 'CUST-' . (++$codeCounter);
            $email = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $company)) . '@corporate-client.com';
            $phone = '+1 (' . rand(200, 999) . ') ' . rand(100, 999) . '-' . rand(1000, 9999);

            // Customers registered between 2024-01-01 and 2026-06-01
            $daysAgo = rand(150, 700);
            $custSince = date('Y-m-d', strtotime("-{$daysAgo} days"));

            Database::execute(
                "INSERT INTO customers (customer_code, name, email, phone, city, state, region_id, customer_since)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [$code, $company, $email, $phone, $loc[0], $loc[1], $loc[2], $custSince]
            );
        }
    }

    private function seedOrdersAndItems(): void
    {
        echo "-> Seeding Orders & Order Items across 18 Months...\n";

        $customers = Database::fetchAll("SELECT id, region_id, customer_since FROM customers");
        $products = Database::fetchAll("SELECT id, selling_price, cost_price, sku FROM products");
        $salespersons = Database::fetchAll("SELECT id, region_id FROM salespersons");

        $salesByRegion = [];
        foreach ($salespersons as $sp) {
            $salesByRegion[$sp['region_id']][] = $sp['id'];
        }

        $channels = ['Direct Sales', 'Partner Portal', 'Online Store', 'Enterprise RFP'];

        $orderNum = 50000;
        $orderCount = 0;
        $itemCount = 0;
        $returnCount = 0;

        // Simulate 18 months from 2025-05-01 to 2026-11-01
        $startDate = strtotime('-18 months');
        $endDate = time();

        Database::beginTransaction();

        for ($t = $startDate; $t <= $endDate; $t += (3600 * rand(3, 8))) {
            $orderDate = date('Y-m-d', $t);
            $month = (int) date('m', $t);
            $year = (int) date('Y', $t);

            // Scenario: May 2026 Supply Chain Dip / Anomaly
            $isDipMonth = ($year === 2026 && $month === 5);
            if ($isDipMonth && rand(1, 100) > 40) {
                continue; // drop 60% orders in May 2026 for anomaly detection!
            }

            // Pick customer
            $cust = $customers[array_rand($customers)];
            if ($orderDate < $cust['customer_since']) {
                continue;
            }

            // Scenario: Churning customers
            // If customer ID between 10 and 25, simulate they churned 90 days ago!
            if ($cust['id'] >= 10 && $cust['id'] <= 25 && $t > strtotime('-75 days')) {
                continue; // At-Risk / Churned segment
            }

            // Scenario: Midwest underperformance (region 3)
            if ($cust['region_id'] == 3 && rand(1, 100) > 65) {
                continue; // 35% less orders in Midwest
            }

            $regionId = $cust['region_id'];
            $salesId = !empty($salesByRegion[$regionId]) ? $salesByRegion[$regionId][array_rand($salesByRegion[$regionId])] : null;
            $channel = $channels[array_rand($channels)];
            $orderNumber = 'ORD-' . (++$orderNum);

            // Create order with 0 initially
            Database::execute(
                "INSERT INTO orders (order_number, customer_id, salesperson_id, region_id, order_date, channel, status, subtotal, discount, tax, total_amount)
                 VALUES (?, ?, ?, ?, ?, ?, 'Completed', 0, 0, 0, 0)",
                [$orderNumber, $cust['id'], $salesId, $regionId, $orderDate, $channel]
            );
            $orderId = Database::lastInsertId();
            $orderCount++;

            // Create 1 to 4 items
            $numItems = rand(1, 4);
            $subtotal = 0.0;
            $totalCost = 0.0;
            $totalProfit = 0.0;

            for ($i = 0; $i < $numItems; $i++) {
                $p = $products[array_rand($products)];
                $qty = rand(1, 6);
                $unitPrice = (float) $p['selling_price'];
                $unitCost = (float) $p['cost_price'];
                $itemDiscount = (rand(1, 10) > 7) ? round($unitPrice * 0.10, 2) : 0.0; // occasional 10% discount
                $lineSubtotal = round(($unitPrice - $itemDiscount) * $qty, 2);
                $lineCost = round($unitCost * $qty, 2);
                $lineProfit = round($lineSubtotal - $lineCost, 2);

                Database::execute(
                    "INSERT INTO order_items (order_id, product_id, quantity, unit_price, discount, cost, profit)
                     VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [$orderId, $p['id'], $qty, $unitPrice, $itemDiscount * $qty, $lineCost, $lineProfit]
                );
                $itemId = Database::lastInsertId();
                $itemCount++;

                $subtotal += $lineSubtotal;
                $totalCost += $lineCost;
                $totalProfit += $lineProfit;

                // Scenario: High return rate on SKU-CLD-05 (Edge node)
                $isCloudEdge = ($p['sku'] === 'SKU-CLD-05');
                $returnChance = $isCloudEdge ? 35 : 4; // 35% return rate vs 4% baseline

                if (rand(1, 100) <= $returnChance) {
                    $returnDate = date('Y-m-d', min(time(), strtotime($orderDate . ' +' . rand(3, 14) . ' days')));
                    $reason = $isCloudEdge ? 'Thermal throttling & firmware incompatibility' : 'Client downsized project scope';
                    Database::execute(
                        "INSERT INTO returns (order_id, order_item_id, return_date, reason, refund_amount, status)
                         VALUES (?, ?, ?, ?, ?, 'Processed')",
                        [$orderId, $itemId, $returnDate, $reason, $lineSubtotal]
                    );
                    $returnCount++;
                }
            }

            $discount = round($subtotal * (rand(0, 10) > 8 ? 0.05 : 0.0), 2);
            $tax = round(($subtotal - $discount) * 0.08, 2);
            $totalAmount = round($subtotal - $discount + $tax, 2);

            Database::execute(
                "UPDATE orders SET subtotal = ?, discount = ?, tax = ?, total_amount = ? WHERE id = ?",
                [$subtotal, $discount, $tax, $totalAmount, $orderId]
            );
        }

        Database::commit();
        echo "[OK] Seeded {$orderCount} orders, {$itemCount} items, and {$returnCount} returns.\n";
    }

    private function seedCampaigns(): void
    {
        echo "-> Seeding Marketing Campaigns & Results...\n";

        $campaigns = [
            ['Q1 Cloud Migration Blitz', 'Google Ads', 45000.00, '2026-01-10', '2026-03-31', 'DevOps & IT Directors', 'Completed'],
            ['Cybersecurity Zero-Trust Push', 'LinkedIn Ads', 60000.00, '2026-02-01', '2026-05-15', 'CISOs & Security Architects', 'Completed'],
            ['Enterprise AI Hackathon Sponsorship', 'Event / Sponsorship', 35000.00, '2026-04-01', '2026-04-30', 'Data Scientists & ML Engineers', 'Completed'],
            ['Mid-Market SaaS Retargeting', 'Display Network', 25000.00, '2026-05-01', '2026-07-31', 'VP Technology & CTOs', 'Completed'],
            ['Q3 Multi-Cloud Infrastructure Summit', 'Webinar Series', 20000.00, '2026-07-15', '2026-09-30', 'System Architects', 'Completed'],
            ['End-of-Year Security Renewal Promo', 'Email Marketing', 15000.00, '2026-09-01', '2026-11-30', 'Existing Accounts', 'Active'],
            ['GenAI Workflow Agent Launch', 'Omnichannel Blitz', 75000.00, '2026-10-01', '2026-12-31', 'Enterprise Decision Makers', 'Active']
        ];

        foreach ($campaigns as $camp) {
            Database::execute(
                "INSERT INTO campaigns (name, channel, budget, start_date, end_date, target_audience, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                $camp
            );
            $campId = Database::lastInsertId();

            // Seed monthly results
            $impressions = rand(80000, 350000);
            $clicks = round($impressions * (rand(18, 38) / 1000));
            $leads = round($clicks * (rand(6, 14) / 100));
            $conversions = round($leads * (rand(10, 25) / 100));
            $revenue = $conversions * rand(2800, 8500);

            Database::execute(
                "INSERT INTO campaign_results (campaign_id, impressions, clicks, leads, conversions, revenue_generated, recorded_date)
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                [$campId, $impressions, $clicks, $leads, $conversions, $revenue, $camp[3]]
            );
        }
    }

    private function seedTargets(): void
    {
        echo "-> Seeding Monthly Targets for 2025 & 2026...\n";

        // Seed 18 months of targets with baseline $220,000 - $450,000 revenue
        $months = [
            [2025, 5, 230000, 92000, 140],
            [2025, 6, 245000, 98000, 150],
            [2025, 7, 260000, 104000, 160],
            [2025, 8, 255000, 102000, 155],
            [2025, 9, 280000, 115000, 175],
            [2025, 10, 310000, 128000, 190],
            [2025, 11, 350000, 145000, 220],
            [2025, 12, 390000, 160000, 245],
            [2026, 1, 280000, 115000, 170],
            [2026, 2, 295000, 120000, 180],
            [2026, 3, 330000, 138000, 205],
            [2026, 4, 345000, 142000, 215],
            [2026, 5, 360000, 150000, 225], // May 2026 target high, actual had dip!
            [2026, 6, 375000, 158000, 235],
            [2026, 7, 390000, 165000, 245],
            [2026, 8, 385000, 162000, 240],
            [2026, 9, 410000, 175000, 260],
            [2026, 10, 430000, 185000, 275],
            [2026, 11, 450000, 195000, 290]
        ];

        foreach ($months as $m) {
            Database::execute(
                "INSERT INTO targets (year, month, dimension_type, dimension_id, target_revenue, target_profit, target_orders)
                 VALUES (?, ?, 'overall', NULL, ?, ?, ?)",
                $m
            );
        }
    }
}

