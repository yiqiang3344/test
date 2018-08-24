<?php
/**
 * Created by PhpStorm.
 * User: sidney
 * Date: 2018/8/24
 * Time: 下午2:56
 */

namespace p2p\model;


/**
 * Class LendRecord
 * @package p2p
 * @property Loan $loan
 * @property TradeOrder $order
 * @property Coupon $coupon
 * @property int $id
 * @property string $trade_number 交易单号
 * @property string $pay_number 支付单号
 * @property string $loan_number 标的信息
 * @property int $lender 投资人
 * @property int $amount 投资本金
 * @property int $coupon_id 优惠券ID
 * @property float $rate 年华收益率
 * @property float $add_rate 加息
 * @property int $interest 应回利息
 * @property int $fee 应回费用
 * @property string $status 状态
 * @property int $paid_principal 已回本金
 * @property int $paid_interest 已回利息
 * @property int $paid_fee 已回费用
 * @property string $created_time 创建时间
 */
class LendRecord extends Base
{
    const STATUS_NONE = 'none';
    const STATUS_SUCCESS = 'success';
    const STATUS_ABORTION = 'abortion';
    const STATUS_FAILED = 'failed';
    public static $statusMap = [
        self::STATUS_NONE => ['name' => '投标中'],
        self::STATUS_SUCCESS => ['name' => '成功'],
        self::STATUS_ABORTION => ['name' => '流标'],
        self::STATUS_FAILED => ['name' => '失败'],
    ];

    public function failed()
    {
        $this->status = self::STATUS_FAILED;
        return $this;
    }

    public function lend()
    {
        $loan = $this->loan;
        //判断标是否已满
        if ($loan->isFull()) {
            $this->failed();
            return $this->buildError('100000', '已满标');
        }

        //获取借贷记录
        $loanRecords = $loan->getLoanRecords();
        $pos = $loan->last_lend_pos;
        $maxPos = count($loanRecords) - 1;

        $portion = 100; //每一份多少钱

        //计算出给每个借贷人投资多少钱
        $lendRecordMap = [];
        $amount = $this->amount;

        while ($amount > 0) {
            if (!isset($lendRecordMap[$loanRecords[$pos]->user_id])) {
                $lendRecordMap[$loanRecords[$pos]->user_id] = 0;
            }
            $lendRecordMap[$loanRecords[$pos]->user_id] += $portion;
            $pos++;
            $pos = $pos % $maxPos;
            $amount -= $portion;
        }

        //更新标的投资位置
        $loan->last_lend_pos = $pos;

        //生成投资明细
        \Output::$list['lendDetailList'] = [];
        $id = 1;
        foreach ($lendRecordMap as $k => $value) {
            $m = new LendDetail([
                'id' => $id++,
                'trade_number' => $this->order->trade_number,
                'loan_number' => $this->loan->loan_number,
                'lender' => $this->order->user_id,
                'loaner' => $k,
                'rate' => $this->loan->protocol_rate,
                'add_rate' => isset($this->coupon) ? $this->coupon->add_rate : 0,
                'amount' => $value,
                'interest' => '',
                'fee' => '',
                'status' => self::STATUS_NONE,
                'created_time' => date('Y-m-d H:i:s'),
            ]);
            $m->lendRecord = $this;
            $m->generateCapitalRecord();
            $m->calculateInterest();
            $this->interest += $m->interest;
            $this->fee += $m->fee;
            \Output::$list['lendDetailList'][] = $m;
        }

        return true;
    }
}