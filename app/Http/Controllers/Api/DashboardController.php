<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\CashRegisterLog;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use function GuzzleHttp\Promise\all;
use Carbon\Carbon;
use App\Libraries\AllSettingFormat;
use App\Models\OrderItems;
use App\Models\Payments;


use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    public function getAllData()
    {
        try {
            $basicData = $this->getBasicData();
            $barChartData = $this->barChartData();
            $lineChartData = $this->lineChartData();
            $branchData = $this->getBranchData();

            $responseData = [
                'basicData' => $basicData,
                'barChartData' => $barChartData,
                'lineChartData' => $lineChartData,
                'branchData' => $branchData,
            ];

            // Pretty print the JSON response
            return response()->json($responseData, 200, [], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            // Handle exceptions and return error response
            return response()->json([
                'error' => 'An error occurred while fetching data.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function getBranchData()
    {
        $branch_id = Auth()->guard('api')->user()->branch_id;
        $branch = Branch::find($branch_id);
        $data = array();
        $data['branch_name'] = $branch->name;
        return $data;
    }
    public function getBasicData()
    {
        $today = Carbon::today()->toDateString();
        $date = Carbon::today()->subDays(30);
        $date = date('Y-m-d', strtotime($date));

        $data = array();

        $data['todaySales'] = (string) Order::todaysSale($today);
        $data['monthlySale'] = (string) Order::monthlySold($date);
        $data['totalSale'] = (string) Order::totalSold();
        $data['totalReturn'] = '0'; // Assuming totalReturn should also be a string
        $data['todayProfit'] = (string) Order::todayProfit($today);
        $data['monthlyProfit'] = (string) Order::monthlyProfit($date);
        $data['totalProfit'] = (string) Order::totalProfit();

        return $data;
    }


    public function barChartData()
    {
        $year = date("Y");

        $monthlySale = OrderItems::monthlySale($year);
        $monthlyArraySale = $this->manipulateBarChart($monthlySale, 'sales');
        $monthlyReceive = OrderItems::monthlyReceive($year);
        $monthlyArrayReceive = $this->manipulateBarChart($monthlyReceive, 'receive');

        $monthlyProfit = OrderItems::monthlyProfit($year);
        $monthlyArrayProfit = $this->manipulateBarChart($monthlyProfit, 'profit');

        // Convert all values to strings
        $monthlyArrayReceive = array_map('strval', $monthlyArrayReceive);
        $monthlyArraySale = array_map('strval', $monthlyArraySale);
        $monthlyArrayProfit = array_map('strval', $monthlyArrayProfit);

        return ['receiving' => $monthlyArrayReceive, 'sales' => $monthlyArraySale, 'profit' => $monthlyArrayProfit];
    }


    public function manipulateBarChart($chartData, $key)
    {
        $dataArray = array_fill(0, 12, '0.0'); // Initialize with '0.0'

        foreach ($chartData as $data) {
            $dataArray[$data->month - 1] = strval($data[$key]);
        }

        return $dataArray;
    }

    public function lineChartData()
    {
        $profit = array();
        $days = array();

        $sevenDaysProfit = Order::getSevenDaysProfit();

        foreach ($sevenDaysProfit as $dailyProfit) {
            $date = $dailyProfit->date;
            $day = date("D", strtotime($date));
            array_push($profit, strval($dailyProfit->profit));
            array_push($days, $day);
        }

        return ['days' => $days, 'profit' => $profit];
    }
}
