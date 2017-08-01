<?php
/**
 * 身份证号码处理
 */
class identity
{
    private $identity;
    public function __construct($identity)
    {
        // 判断身份证真实性
        $card = (string) $identity; //身份证号码
        $map  = array(1, 0, X, 9, 8, 7, 6, 5, 4, 3, 2);
        $sum  = 0;
        for ($i = 17; $i > 0; $i--) {
            $s = pow(2, $i) % 11;
            $sum += $s * $card[17 - $i];
        }
        // $map[$sum % 11];//这里显示最后一位校验码
        if ($map[$sum % 11] == substr($card, 17, 1)) {
            $this->$identity = $identity;
        } elseif ($map[$sum % 11] == 'X' || substr($card, 17, 1) == 'x') {
            $this->$identity = $identity;
        } else {
            return false;
        }
    }

    /**
     * 通过身份证号码获取性别 男1 女0
     */
    public function identity_get_sex($identity)
    {
        return $sex = (substr($identity, 16, 1) % 2) ? 1 : 0;
    }

    /**
     * 通过身份证号码获取生日
     */
    public function identity_get_birthday($identity)
    {
        $year  = ((int) substr($identity, 6, 4)); //取得年份
        $month = ((int) substr($identity, 10, 2)); //取得月份
        $day   = ((int) substr($identity, 12, 2)); //取得几号
        // return $birthday = $year . '-' . $month . '-' . $day; // 返回文本
        return mktime(0, 0, 0, $month, $day, $year); // 返回时间戳
    }

    /**
     * 通过身份证号码获取籍贯，需要调用数据库（配合目录下.sql文件使用）
     */
    public function identity_get_native($identity)
    {
        $id6    = substr($identity, 0, 6);
        $result = dbq("SELECT ad FROM `system_idtoad` WHERE id=$id6");
        $row    = mysql_fetch_assoc($result);
        if ($row['ad']) {
            return $row['ad'];
        } else {
            // 处理已经过时的区县，只显示到市级
            $id6n   = substr($id6, 0, 4) . '00';
            $result = dbq("SELECT ad FROM `system_idtoad` WHERE id=$id6n");
            $row    = mysql_fetch_assoc($result);
            return $row['ad'];
        }
    }
}

$identity = '33022119670222791X';
if (new identity) {
    # code...
}
