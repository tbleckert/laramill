Billing
=======

Billing is a Laravel package that provides a powerful bridge to Paymill that makes it easy to handle payments and subscriptions

- [Install](#install)
- [Configuration](#configure)
- [Offers](#offers)
- [Clients](#clients)
- [Payments](#payments)
- [Subscriptions](#subscriptions)

## Install

Simply add Billing to your `composer.json`:

	"tbleckert/billing": "dev-master"

## Configure

To start using Billing you need to publish the config files:

	php artisan config:publish tbleckert/billing
	
Then fill in your public and private Paymill keys in your new config located at `app/config/packages/tbleckert/billing/config.php`

### Offers

To add Paymill offers/plans you open the config file (see above) and fill the offers array as follows:

	'offers'  => array(
		'Basic' => array(
			'monthly'  => 'offer_key',
			'annually' => 'offer_key'
		),
		'Special' => array(
			'daily'  => 'offer_key',
			'weekly' => 'offer_key'
		)
	)
	
Each offer has a name and an array containing offer keys for each payment interval that the offer supports. As an example, you would use this code to subscribe a user to the basic plan with annual payment:

	$user = User::find(1);
	$user->subscription('Basic', 'annually')->create($token);
	
More about subscriptions further down.

## Clients

Each user needs a client in Paymill. I suggest that you set up a client in the user registration step (even if you support free accounts). This way, you have the user prepared for subscriptions and payments.

### Create client

The `email` column will be used by default as the client email.

	$user->client()->create();
	
To set a different email, you can just pass it as a parameter in the `create` method:

	$user->client()->create('myemail@domain.com');
	
You can also add an optional description text

	$user->client()->create('myemail@domain.com', 'Client description');
	
### Update client

Updating a client is very similar to creating one:

	$user->client()->update('myemail@domain.com', 'Client description');
	
### Remove client

To remove a client from paymill, simply use the `remove` method:

	$user->client()->remove();

## Payments

For any subscription or transaction, the client needs a payment. To create a payment we need to use the Paymill Bridge. The Bridge generates a token that we need when creating our payment.

### Create payment

For the token generation, please have a look at the [official Paymill documentation](https://www.paymill.com/en-gb/documentation-3/introduction/payment-form/). Then, for the back-end:

	$token = Input::get('paymillToken');
	$user->payment($token)->create();
	
### Update payment

There's no functionality for updating a payment, since that makes no sense. Instead, just create a new one and if you want, remove the old one.

### Remove payment

	$user->payment(false, 'payment_id')->remove();
	
### Payment details

The details for a payment can give you information like card type, last four card numbers and more.

	$user->payment(false, 'payment_id')->details();
	
### List all payments

To get all payments created for a user, use the `all` method:

	$user->payment()->all();
	
## Subscriptions

Subscriptions connects a client to an offer with a payment. Paymill handles the payments automatically on the given interval.

### Create subscription

For a subscription to work, the client needs a payment. You can either pass a payment id to the subscription method or let Billing automatically set the last registered payment. If the user already have a subscription, the create method will throw an exception.

	$user->subscription('Basic', 'annually')->create(); // Alternative 1
	$user->subscription('Basic', 'annually', 'pay_id')->create(); // Alternative 2
	
### Subscription details

Since the subscription id is saved to the database, you don't have to pass any parameter.

	$user->subscription()->details();
	
### Update subscription

You can update a subscription to move the client to another offer or if the client has a new or updated payment.

	$user->subscription('Basic', 'monthly')->update();
	
### Cancel subscription

At the moment, canceling a subscription will remove the client immediately from the subscription.

	$user->subscription()->remove();
	
### List all subscriptions

At the moment, Billing only supports 1 subscription per user, but the `all` method still exists:

	$user->subscription()->all();
	
