<?php
abstract class MLCStripeMode{
	const TEST = 'test';
	const LIVE = 'live';
}
abstract class MLCStripeType{
	const CARD = 'card';
	const CUSTOMER = 'customer';
}
abstract class MLCStripeChargeQueryParam{
	const count = 'count';//Default 10, Can be between 1 - 100
	const created = 'created';
	const created_gt = 'gt';
	const created_gte = 'gte';
	const created_lt = 'lt';
	const created_lte = 'lte';
	const customer = 'customer';
	const offset = 'offset';
}
