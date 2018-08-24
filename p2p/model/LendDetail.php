<?php
/**
 * Created by PhpStorm.
 * User: sidney
 * Date: 2018/8/24
 * Time: 下午2:56
 */

namespace p2p\model;

/**
 * Class LendDetail
 * @package p2p
 * @property LendRecord $lendRecord
 * @property int $id
 * @property string $trade_number 交易单号
 * @property string $loan_number 标的信息
 * @property int $lender 投资人
 * @property int $loaner 借款人
 * @property int $amount 投资金额
 * @property int $interest 应回利息
 * @property int $fee 应回费用
 * @property string $status 状态
 * @property string $created_time 投资时间
 */
class LendDetail extends Base
{
    /**
     * 生成资金明细
     * @return bool
     */
    public function generateCapitalRecord()
    {
        !isset(\Output::$list['capitalRecordList']) && (\Output::$list['capitalRecordList'] = []);
        //生成资金记录
        $capitalRecord = new CapitalRecord([
            'user_id' => $this->lender, //用户id
            'trade_number' => $this->trade_number, //交易单号
            'type' => TradeOrder::TYPE_LEND, //交易类型
            'relation_id' => $this->id, //关联id
            'in_or_pay' => 'pay', //收还是支
            'amount' => $this->amount, //金额
            'remark' => '投资', //备注
            'is_frozen' => 0, //是否冻结
            'created_time' => date('Y-m-d H:i:s'), //创建时间
        ]);
        \Output::$list['capitalRecordList'][] = $capitalRecord;
        return true;
    }

    /**
     * 计息
     * @return bool
     */
    public function calculateInterest()
    {
        $loan = $this->lendRecord->loan;
        $repayInfo = Loan::calculateInterest($loan->payment_method, $this->lendRecord->rate + $this->lendRecord->add_rate, 1, $this->amount)[0];
        $this->interest = $repayInfo['interest'];
        $this->fee = $repayInfo['fee'];
        return true;
    }
}