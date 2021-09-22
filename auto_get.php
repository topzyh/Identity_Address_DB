<?php
/**
 * 自动拉取最新的列表
 * @author TopZYH <zyh.95@163.com>
 */

// 获取数据
$source_data = file_get_contents('https://way.jd.com/RTBAsia/dictionary_district?appkey=9b066f875202e509f662b86efdc990d8');
$source_data = json_decode($source_data, true);
if (empty($source_data['result'])) {
	die("未拉取到数据:" . $source_data['msg']);
}

// 数据格式化
$source_data = explode("\n", $source_data['result']);
$data = [];
foreach ($source_data as $value) {
	$value = explode(',', $value);
	$data[(int) $value[0]] = $value;
}
unset($source_data);
// var_dump($data); // 有效数据

// 连接数据库
$conn = new mysqli('localhost:3306', 'root', 'topsts'); //连接数据库
if ($conn == false) {
	die("数据连接失败！");
} else {
	echo "数据连接成功！<br>\n";
}
$conn->query("set names 'utf8'"); //数据库输出编码
$conn->select_db('test'); //打开数据库

// 先把所有status=0
$sql = "UPDATE `system_idtoad` SET status=0";
if ($conn->query($sql) != true) {
	echo "Error insert data: " . $conn->error . 'SQL:' . $sql . "<br>\n";
}

// 写入数据库
echo "本次新增:<br>\n";
foreach ($data as $value) {
	$result = $conn->query("SELECT * FROM `idtoad` WHERE id=$value[0]");
	if ($temp = $result->fetch_assoc()) {
		$sql = "UPDATE `idtoad` SET county_name='$value[1]',city_name='$value[2]',province_name='$value[3]',status=1 WHERE id=$value[0]";
		if ($conn->query($sql) != true) {
			echo "Error insert data: " . $conn->error . 'SQL:' . $sql . "<br>\n";
		}
	} else {
		print_r($value);
		echo "<br>\n"; // 输出新增
		$sql = "INSERT INTO idtoad (id, province_name, city_name, county_name, status) VALUES ($value[0], '$value[3]', '$value[2]', '$value[1]', 1)";
		if ($conn->query($sql) != true) {
			echo "Error insert data: " . $conn->error . 'SQL:' . $sql . "<br>\n";
		}
	}
}
echo "更新完成！ <br>\n";

// 更新 省市区id
echo "更新 省市区id <br>\n";
$result = $conn->query("SELECT * FROM `idtoad` WHERE province_id=0");

while ($row = $result->fetch_assoc()) {
	var_dump($row);
	$_province_id = substr($row['id'], 0, 2);
	$_city_id = substr($row['id'], 2, 2);
	$_county_id = substr($row['id'], 4, 2);
	$sql = "UPDATE `idtoad` SET county_id=" . $_county_id . ",city_id=" . $_city_id . ",province_id=" . $_province_id . " WHERE id=" . $row['id'];
	if ($conn->query($sql) != true) {
		echo "Error insert data: " . $conn->error . 'SQL:' . $sql . "<br>\n";
	}
}
echo "更新 省市区id 完成<br>\n";
