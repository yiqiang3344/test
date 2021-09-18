<?php
/**
 * Created by PhpStorm.
 * User: sidney
 * Date: 2018/8/24
 * Time: 下午2:55
 */

namespace p2p\model;


/**
 * Class Loan
 * @package p2p
 * @property string $type 分类
 * @property string $type_name 分类名称
 * @property string $loan_number 标的信息
 * @property float $protocol_rate 协议年华收益率
 * @property float $bad_rate 坏账率
 * @property float $expect_rate 预计年华收益率
 * @property int $min_lend_amount 起投金额
 * @property int $amount 资产金额
 * @property int $lend_amount 已投金额
 * @property string $interest_method 计息方式
 * @property int $period 周期数
 * @property string $status 已结清
 * @property string $payment_method 还款方式
 * @property float $add_rate 加息
 * @property string $start_time 起始日期
 * @property string $interest_time 计息日期
 * @property string $end_time 到期日期
 */
class Loan extends Base
{
    const PAYMENT_METHOD_EQUAL_PRINCIPAL = '等额本息';

    public $last_lend_pos = 0;

    public static function publish($params)
    {
        return new self($params);
    }

    /**
     * 标是否已满
     * @return bool
     */
    public function isFull()
    {
        return false;
    }

    /**
     * 投标
     * @param TradeOrder $order
     * @return bool
     */
    public function lend(TradeOrder $order)
    {
        $coupon = null;
        if (mt_rand() / mt_getrandmax() * 100 > 50) {
            $coupon = new Coupon([
                'id' => 1,
                'add_rate' => 0.01,
            ]);
        }

        //生成投标记录
        $lendRecord = new LendRecord([
            'trade_number' => $order->trade_number, //交易单号
            'pay_number' => $order->pay_number, //支付单号
            'loan_number' => $order->loan_number, //标的信息
            'lender' => $order->user_id, //投资人
            'amount' => $order->amount, //投资本金
            'coupon_id' => $coupon ? $coupon->id : 0, //优惠券ID
            'rate' => $this->protocol_rate, //加息
            'add_rate' => $coupon ? $coupon->add_rate : 0, //加息
            'interest' => 0, //应回利息
            'fee' => 0, //应回费用
            'status' => 'none', //状态
            'paid_principal' => 0, //已回本金
            'paid_interest' => 0, //已回利息
            'paid_fee' => 0, //已回费用
            'created_time' => date('Y-m-d H:i:s'), //创建时间
        ]);

        $lendRecord->loan = $order->loan;
        $lendRecord->order = $order;

        \Output::$list['lendRecord'] = $lendRecord;

        if (false === $lendRecord->lend()) {
            return $this->buildError($lendRecord);
        }

        return true;
    }

    /**
     * 获取借贷记录列表
     * @return LoanRecord[]
     */
    public function getLoanRecords()
    {
        $list = [];
        $sum = 0;
        for ($i = 1; $i <= 10; $i++) {
            $sum += $amount = mt_rand(1, 9) * 1000;
            if ($i == 10) {
                $amount = $this->amount - $sum;
            }
            $list[] = new LoanRecord([
                'user_id' => $i,
                'amount' => $amount,
            ]);
        }
        return $list;
    }

    /**
     * 计息
     * @param $payment_method
     * @param $rate
     * @param $period
     * @param $amount
     * @return array
     */
    public static function calculateInterest($payment_method, $rate, $period, $amount)
    {
        $repayList = [];
        $repay = [
            'period' => 0,
            'amount' => 0,
            'interest' => 0,
            'fee' => 0,
        ];
        if ($payment_method == Loan::PAYMENT_METHOD_EQUAL_PRINCIPAL) {
            $totalInterest = bcdiv($amount * $rate, 12, 2);
            $principal = bcdiv($amount, $period, 2);
            $interest = bcdiv($totalInterest, $period, 2);
            $sumPrincipal = $sumInterest = 0;
            for ($i = 1; $i <= $period; $i++) {
                $tmpRepay = $repay;
                if ($i == $period) {
                    $tmpRepay['principal'] = $amount - $sumPrincipal;
                    $tmpRepay['interest'] = $totalInterest - $sumInterest;
                } else {
                    $sumPrincipal += $tmpRepay['principal'] = $principal;
                    $sumInterest += $tmpRepay['interest'] = $interest;
                }
                $repayList[] = $tmpRepay;
            }
        }
        return $repayList;
    }
}