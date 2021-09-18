<?php
/**
 * Created by PhpStorm.
 * User: sidney
 * Date: 2018/8/24
 * Time: 下午2:53
 */

include '../autoload.php';

const ERROR_MESSAGE = [
    '000000' => '操作成功',
    '100000' => '',
];


Class Output
{
    public static $list = [];

    public static function out($message)
    {
        var_dump($message, self::$list);
        return false;
    }

    public static function show($message)
    {
        echo $message, "\n";
    }
}

//发布标
$loan = \p2p\model\Loan::publish([
    "type" => "normal", //分类
    "type_name" => "个人借款包", //分类名称
    "loan_number" => "XYF20170723001", //标的信息
    "protocol_rate" => 0.24, //协议年华收益率
    "bad_rate" => 0.01, //坏账率
    "expect_rate" => 0.01, //预计年华收益率
    "min_lend_amount" => 100, //起投金额
    "amount" => 1000000, //资产金额
    "lend_amount" => 1000000, //已投金额
    "interest_method" => "月", //计息方式
    "period" => 1, //周期数
    "status" => "collecting", //状态：collecting 募集中，full 满标，remit 放款中，normal 正常还款，overdue 逾期，paid 已结清
    "payment_method" => \p2p\model\Loan::PAYMENT_METHOD_EQUAL_PRINCIPAL, //还款方式
    "add_rate" => 0.02, //加息
    "start_time" => "2018-08-01", //起始日期
    "interest_time" => "2018-08-01", //计息日期
    "end_time" => "2018-08-01", //到期日期
]);
Output::$list['loan'] = $loan;

//投资人
$lender = new \p2p\model\Lender([
    'user_id' => 111111, //资产总额
    'asset' => 10000, //资产总额
    'income' => 0, //累计收益
    'total_amount' => 10000, //余额
    'amount' => 10000, //可用余额
    'frozen_amount' => 0, //冻结金额
    'collecting_principal' => 0, //募集中本金
    'repaying_principal' => 0, //正常待还本金
    'overdue_principal' => 0, //逾期待还本金
]);
Output::$list['lender'] = $lender;

//生成订单
$order = new \p2p\model\TradeOrder([
    'user_id' => $lender->user_id, //用户id
    'trade_number' => date('YmdHis') . mt_rand(100000, 999999), //交易单号
    'pay_number' => '', //支付单号
    'type' => 'lend', //交易类型
    'amount' => 10000, //金额
    'bank' => '招商银行', //银行
    'bank_branch' => '南泉路支行', //支行
    'bank_card_number' => '1234', //银行卡
    'bank_mobile' => '18621927051', //预留手机号
    'loan_number' => 'XYF201808201001', //标的信息
    'remark' => '第一次我投标哦', //备注
    'status' => 'none', //状态
    'created_time' => date('Y-m-d H:i:s'), //创建时间
]);
Output::$list['order'] = $order;

$order->loan = $loan;
$order->lender = $lender;

//支付
if ($order->pay($lender) === false) {
    return Output::out($order->getError());
}

//订单成功
if ($order->success() === false) {
    return Output::out($order->getError());
}

Output::show('融资：1000000');
Output::show('投资：10000');
Output::show('利息：' . Output::$list['lendRecord']->interest);
$totalAmount = $totalInterest = 0;
foreach (Output::$list['lendDetailList'] as $k => $item) {
    /** @var \p2p\model\LendDetail $item */
    Output::show('本金' . $k . '：' . $item->amount);
    Output::show('利息' . $k . '：' . $item->interest);
    $totalAmount += $item->amount;
    $totalInterest += $item->interest;
}
Output::show('本金求和：' . $totalAmount);
Output::show('利息求和：' . $totalInterest);