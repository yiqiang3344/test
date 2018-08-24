<?php
/**
 * Created by PhpStorm.
 * User: sidney
 * Date: 2018/8/24
 * Time: 下午2:55
 */

namespace p2p\model;


/**
 * Class TradeOrder
 * @package p2p
 * @property Loan $loan
 * @property Lender $lender
 * @property int $user_id 用户id
 * @property string $trade_number 交易单号
 * @property string $pay_number 支付单号
 * @property string $type 交易类型
 * @property int $amount 金额
 * @property string $bank 银行
 * @property string $bank_branch 支行
 * @property string $bank_card_number 银行卡
 * @property string $bank_mobile 预留手机号
 * @property string $loan_number 标的信息
 * @property string $remark 备注
 * @property string $status 状态
 * @property string $created_time 创建时间
 */
class TradeOrder extends Base
{
    const STATUS_NONE = 'none';
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    public static $statusMap = [
        self::STATUS_NONE => ['name' => '待处理',],
        self::STATUS_PENDING => ['name' => '支付中',],
        self::STATUS_SUCCESS => ['name' => '成功',],
        self::STATUS_FAILED => ['name' => '失败',],
    ];

    const TYPE_LEND = 'lend';
    const TYPE_REPAY = 'repay';
    const TYPE_RECHARGE = 'recharge';
    const TYPE_WITHDRAW = 'withdraw';
    public static $typeMap = [
        self::TYPE_LEND => ['name' => '投资', 'need_pay' => false,],
        self::TYPE_REPAY => ['name' => '还款', 'need_pay' => false,],
        self::TYPE_RECHARGE => ['name' => '充值', 'need_pay' => true,],
        self::TYPE_WITHDRAW => ['name' => '提现', 'need_pay' => true,],
    ];

    public function pending()
    {
        $this->status = self::STATUS_PENDING;
        return $this;
    }

    public function fail()
    {
        $this->status = self::STATUS_FAILED;
        return $this;
    }

    public function success()
    {
        if ($this->status == self::STATUS_SUCCESS) {
            return true;
        }
        if ($this->status == self::STATUS_FAILED) {
            return $this->buildError('100000', '状态异常');
        }

        $this->status = self::STATUS_SUCCESS;


        !isset(\Output::$list['capitalRecordList']) && (\Output::$list['capitalRecordList'] = []);
        //生成资金记录
        $capitalRecord = new CapitalRecord([
            'user_id' => $this->user_id, //用户id
            'trade_number' => $this->trade_number, //交易单号
            'type' => $this->type, //交易类型
            'relation_id' => '', //关联id
            'in_or_pay' => 'pay', //收还是支
            'amount' => $this->amount, //金额
            'remark' => $this->remark, //备注
            'is_frozen' => 1, //是否冻结
            'created_time' => date('Y-m-d H:i:s'), //创建时间
        ]);
        \Output::$list['capitalRecordList'][] = $capitalRecord;

        //不同的交易做不同的后续处理
        if ($this->type == self::TYPE_LEND) {
            if ($this->loan->lend($this) === false) {
                return $this->holdError($this->loan);
            }
        } elseif ($this->type == self::TYPE_REPAY) {

        } elseif ($this->type == self::TYPE_RECHARGE) {

        } elseif ($this->type == self::TYPE_WITHDRAW) {

        } else {
            return $this->buildError('100000', '交易类型异常');
        }

        return $this;
    }

    /**
     * 支付
     * @param Lender $lender
     * @return bool
     */
    public function pay(Lender $lender)
    {
        //检查订单类型
        if (!isset(self::$typeMap[$this->type])) {
            return $this->buildError('100000', '交易类型不存在');
        }

        //检查订单类型
        if (self::$typeMap[$this->type]['need_pay']) {
            $this->fail();
            return $this->buildError('100000', '支付失败');
        } elseif ($lender->amount < $this->amount) {
            $this->fail();
            return $this->buildError('100000', '可用余额不足');
        }

        return true;
    }
}