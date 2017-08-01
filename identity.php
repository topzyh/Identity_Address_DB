<?php
/**
 * 身份证号码处理
 * @author TopZYH <zyh.95@163.com>
 */
class identity
{

    /**
     * 判断身份证号码是否正确
     */
    public function is_identity($identity)
    {
        if (strlen($identity) != 18) {
            return false;
        }
        $identity_body     = substr($identity, 0, 17); // 身份证主体(前17位)
        $identity_lastCode = strtoupper(substr($identity, 17, 1)); // 身份证最后一位的验证码
        $factor            = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2); // 加权因子
        $code              = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'); // 校验码对应值
        $checksum          = 0;

        for ($i = 0; $i < strlen($identity_body); $i++) {
            $checksum += substr($identity_body, $i, 1) * $factor[$i];
        }
        // $code[$checksum % 11] 为计算后的最后一位效验码
        if ($code[$checksum % 11] != $identity_lastCode) {
            return false;
        } else {
            return true;
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
