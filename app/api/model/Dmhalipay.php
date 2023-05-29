<?php

//decode by http://www.yunlu99.com/
namespace app\api\model;

class Dmhalipay extends \think\Model
{
	public function pay()
	{
		$callback = "https://" . $_SERVER["SERVER_NAME"] . "/api/Dmhalipaylog/callback";
		$options = ["app_id" => "2021003131634440", "gateway_url" => $callback, "sign_type" => "RSA2", "charset" => "UTF-8", "alipay_public_key" => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsH0whV9UICYtjyD9BXlpPyOOMnpkjQfJl0IAwRqQT2pPmnsUMXta45FDDyKSSllM1YDc5wCro5TmoSvnh1RUZxB8Dklpi0kRzaV0tBAcHv0iJ8HyPeplHjxfrvMYG+yjlFzZCNmFMpJGUi8vhbSMxptqMK/JRPzLzXAvYaFFLP3x79e9J0SZp22lY2ICGYmrMzpw0OoR9fsrxCr/1KUJU/BkpYDl5LmwiMoJw43Kz4vtg3M6qSh2PK9pxtkBtI0tsR3kPO2AU4bjWpuSqBqDCWwoJRvWLSIGfOQpGL3sxeJiifxWfE1Kvq2ujkayVl/nrCmhPO9sr2ILkcyZR4kzFQIDAQAB", "merchant_private_key" => "MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQChSs6ma1ViT+vKQDUNCeiRK176NA9tEg6CobYkk+ZYiE7dS2F65GPwWLAqFo6V0120cxwK2RPSx4pL6mrH0qPtqlOYDPu9o4GH1nWRlqhSGAWxfJYZF0zRjS3fhyaz7yTY0S26o3QM64PwAL9BGcsoKMNIXZeZj7jhUES4KafB/nLzomG2B88Hst/kHNeyQsOI2nqaUNgSPur5VjABIYT4RjsUeSB+cbWhsh0rysRtyMYvwOe/kewg69uCJzH0OSyXTnuOvRPsDUPIDFhrlr/RmuHaE44CVzjFC45rvLqu3a1wKUrGGjO2a61LYhQKPE2i9tIrnEyfm05qQ9NJTpaRAgMBAAECggEBAJZucvZ8PRKIf94OrGwQxbw/u0GYtJqBsM7djEfpOXlxCD0VRUGdKijMTuGyUCIlFMxsX8cuV3LLcI0FgboFF6deqMCzu0dP7EuJFfZHkY2fog+7pzKXrKn1BsfoAYzoinE+B2x/boluT4vU5owNRpGaEyi3QeGTTle2yaEKGNCwyPLfwvGPueDzOjoHYIGIIQegLAdxBp8mrvDhgGfzK+PoiLNGSRYbfIEAFK4AOkzc+CFaUItBIz5GYgJNw8nXJXiPfU8LkMY4DXdd3o5vgdfjeOzrNe/3+uF5nYbb663xbKwsqz0qxMuG0PaN5AcTo0ZCueKc2p8+nPRok5y7ycECgYEA2oCpBYH09R6dmuWcBohkn70nlRr3bvr9oN++eY6Y3N99liKV0AoKq9yA5AYQ63UwuC0RIoZdqSuiTaDBA2hLHb1cCWGhc8/xOxWD1JSveK47njW2VqappLldFHVQdUBAyaWnCaGFtzvX+DZwlAyC5VgirEz/wCZbnMZzGECM7j0CgYEAvPjCWl8vKnSLtyjQancDCRwGCs7A1XbTEFaJnkAPLrn2AiiOgotEiHyVQ5vyZ2HfLt6srhCBJYagyPb+lx6SZ/pGp5ciBpic8sU3J7srOeftdXackECHyeqo3k9f2MPyeBSVjmHrpJvmZbN/N9UmeagzWojtEwx3rlf8UkX1AuUCgYEAxjLEnolWYG4H+jRihg58b+VNnVPbs+CZ5a5vAaZWhKt5KJFwoUgqi+A1TiEugdZIfhfrHg0Rsl0xGQdDbUrSMETO0ner0viBUd3fOhow3N/2ljkUj58X7KJzixYrCGBjJEZNsU/BkgcHjVAcaD69EToUIYSqzGo+2YOtbQdIjuUCgYBrVILW328jlvRwAF93yMbRmrgX1VyTIZ05j100o7+702VVzoE6xi0TtGb28pEYhO9FaLX9W2Ru0utpmVf6ryOKqQ0OTPnHc1DZVohAKb+gcQlfCRJdoDk/xudI9R5EOO7zowVazGURpnaS1wNvy8HBoXYhetnkUtV7EYA/nnpNvQKBgQCOqVfP86QrwqFIm0cUT8SXlX2HDqhVpkCyN16ERQlc/5WUBH0TBlsS2cXfBgqfJMQKSZM0neCxyg1NHkNGfQZXxoLkXRRFobUCPn2iOkrXZH9q2SttVSUeZFhH5NHxxaY0kfbu5bYwrPHsh4U8xDjJOwTnba8RHPZC/v6jwf28WA=="];
		$app = \EasyAlipay\Factory::payment($options);
		$subject = "测试订单";
		$out_trade_no = "Dmh" . time() . rand(0, 9) . rand(0, 9);
		$total_amount = "0.01";
		$response = $app["pay"]->pay($subject, $out_trade_no, $total_amount);
		return htmlspecialchars($response);
	}
}