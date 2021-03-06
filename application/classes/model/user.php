<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Extension of Model_Auth_User
 *
 * Code used from Model_User from Vendo and Synapse Kohana-ACL
 */
class Model_User extends Model_Auth_User implements Model_ACL_User {

	/**
	 * A user has many tokens and roles
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'user_tokens'  => array('model' => 'user_token'),
		'roles'        => array('model' => 'role', 'through' => 'roles_users'),
		'keys'         => array('model' => 'key'),
		'characters'   => array('model' => 'character'),
	);
	
	protected $_has_one = array(
		'profile'      => array('model' => 'profile'),
	);
	
	/**
	 * Rules for the user model. Because the password is _always_ a hash
	 * when it's set,you need to run an additional not_empty rule in your controller
	 * to make sure you didn't hash an empty string. The password rules
	 * should be enforced outside the model or with a model helper method.
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'username' => array(
				array('not_empty'),
				array('max_length', array(':value', 32)),
				// array('alpha_numeric'),
				array(array($this, 'unique'), array('username', ':value')),
			),
			'password' => array(
				array('not_empty'),
			),
			'email' => array(
				array('not_empty'),
				array('email'),
				array(array($this, 'unique'), array('email', ':value')),
			),
		);
	}

	/**
	 * Wrapper method to execute ACL policies. Only returns a boolean, if you
	 * need a specific error code, look at Policy::$last_code
	 *
	 * @from   package  https://github.com/vendo/acl/
	 * @param  string   $policy_name the policy to run
	 * @param  array    $args arguments to pass to the rule
	 * @return boolean
	 */
	public function can($policy_name, $args = array())
	{
		$status = FALSE;
		try
		{
			$refl = new ReflectionClass('Policy_' . $policy_name);
			$class = $refl->newInstanceArgs();
			$status = $class->execute($this, $args);
			if (TRUE === $status)
				return TRUE;
		}
		catch (ReflectionException $ex) // try and find a message based policy
		{
			// Try each of this user's roles to match a policy
			foreach ($this->roles->find_all() as $role)
			{
				$status = Kohana::message('policy', $policy_name.'.'.$role->id);
				
				if ($status)
					return TRUE;
			}
		}
		
		// We don't know what kind of specific error this was
		if (FALSE === $status)
		{
			$status = Policy::GENERAL_FAILURE;
		}
		
		Policy::$last_code = $status;
		
		return TRUE === $status;
	}

	/**
	 * Wrapper method for self::can() but throws an exception instead of bool
	 *
	 * @from   package  https://github.com/vendo/acl/
	 * @param  string   $policy_name the policy to run
	 * @param  array    $args arguments to pass to the rule
	 * @throws Policy_Exception
	 * @return null
	 */
	public function assert($policy_name, $args = array())
	{
		$status = $this->can($policy_name, $args);
	
		if (TRUE !== $status)
		{
			throw new Policy_Exception('Could not authorize policy :policy', array(':policy' => $policy_name), Policy::$last_code);
		}
	}
	
	/**
	 * Updates a user to have a set of roles
	 *
	 * @param   array  List of roles the user should have
	 * @returns TRUE
	 */
	public function update_roles(array $roles)
	{
		foreach (ORM::factory('role')->find_all() as $role)
		{
			$does_have   = $this->is_a($role);
			$should_have = in_array($role->name, $roles);
			
			if ( ! $does_have AND $should_have)
			{
				$this->add('roles', ORM::factory('role', array('name' => $role->name)));
			}
			
			if ($does_have AND ! $should_have)
			{
				$this->remove('roles', ORM::factory('role', array('name' => $role->name)));
			}
		}
		return TRUE;
	}
	
	/**
	 * Searches for users who have a username, real name, or email
	 * that match the given parameter
	 *
	 * @param  string  Term to be matched against
	 * @return object  Database_Result object
	 */
	public static function search($string)
	{
		$query = DB::select()
			->from('users')
			->join('profiles')
			->on('users.id', '=', 'profiles.user_id')
			->where('users.username', 'LIKE', '%'.$string.'%')
			->or_where('users.email', 'LIKE', '%'.$string.'%')
			->or_where('profiles.first_name', 'LIKE', '%'.$string.'%')
			->or_where('profiles.last_name', 'LIKE', '%'.$string.'%');
		
		return $query->execute();	
	}
	
	/**
	 * Determines whether or not a user has (is) a particular role
	 *
	 * @from    pacakge http://github.com/synapsestudios/kohana-acl
	 * @param   mixed   Role to check for
	 * @return  boolean
	 */
	public function is_a($role)
	{
		// Handle guests
		if ($role === Kohana::$config->load('acl.public_role'))
		{
			$login_role = ORM::factory('role', array('name' => 'login'));
			if ( ! $this->loaded() OR ! $this->has('roles', $login_role))
				return TRUE;
			else
				return FALSE;
		}

		// Get role object
		if ( ! $role instanceOf Model_Role)
		{
			$role = ORM::factory('role', array('name' => $role));
		}
		
		// If object failed to load then throw exception
		if ( ! $role->loaded())
			throw new UnexpectedValueException('Tried to check for a role that did not exist.');

		// Return whether or not they have the role
		return (bool) $this->has('roles', $role);
	}

	/**
	 * Alias for is_a() for roles that start with vowels
	 * Yes, I am quite pedantic, actually.
	 *
	 * @param   mixed  Role to check for
	 * @return  boolean
	 */
	public function is_an($role)
	{
		return $this->is_a($role);
	}
	
	/**
	 * Checks to see if the owns a specified model. This theoretically works
	 * for any relationship type.
	 *
	 * @from    pacakge  http://github.com/synapsestudios/kohana-acl
	 * @param   ORM      The object that might be owned
	 * @return  boolean  Whether or not the model is owned by this user
	 */
	public function owns(ORM $model)
	{
		// Get a list of all applicable relationships
		$relationships = $model->belongs_to();
		foreach ($model->has_many() as $alias => $has_many)
		{
			if ( ! empty($has_many['through']))
			{
				$relationships[$alias] = $has_many;
			}
		}

		// Check each applicable relationship
		foreach ($relationships as $alias => $relationship)
		{
			// Make sure the relationship is to the correct model
			if ($relationship['model'] != $this->object_name())
				continue;

			// Check the foreign keys to verify a relationship
			if (isset($relationship['far_key']))
			{
				if ($model->has($alias, $this))
					return TRUE;
			}
			elseif ($model->{$relationship['foreign_key']} == $this->id)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Assigns a role to a User
	 *
	 * @param   mixed  Role to assign
	 * @return  Model_User
	 */
	public function add_role($role)
	{
		// Get role object
		if ( ! $role instanceOf Model_Role)
		{
			$role = ORM::factory('role', array('name' => $role));
		}

		// If object failed to load then throw exception
		if ( ! $role->loaded())
			throw new UnexpectedValueException('Tried to assign a role that did not exist.');

		// Add the role to the user
		$this->add('roles', $role);

		return $this;
	}

	/**
	 * Removes a role from a User
	 *
	 * @param   mixed  Role to remove
	 * @return  Model_User
	 */
	public function remove_role($role)
	{
		// Get role object
		if ( ! $role instanceOf Model_Role)
		{
			$role = ORM::factory('role', array('name' => $role));
		}

		// If object failed to load then throw exception
		if ( ! $role->loaded())
			throw new UnexpectedValueException('Tried to remove a role that did not exist.');

		// Remove the role from the user
		$this->remove('roles', $role);

		return $this;
	}

	/**
	 * Gets a user's email registration or password reset key
	 * Creates a new key if none exists
	 * 
	 * @return string  action key
	 */
	public function get_key($action)
	{
		// Fetch the key from the keys table with the appropriate action
		$record = $this->keys->where('action', '=', $action)->find();
		
		$key = $record->key;
		
		// If no key exists
		if ( ! $key)
		{
			// Create and save the new key
			$key = Text::random('alnum', 30);
			$record->key = $key;
			$record->action = $action;
			$record->user_id = $this->id;
			$record->save();
		}
		return $key;
	}
	
	/**
	 * Resets a user's password to a random string
	 *
	 * @return string  new password
	 */
	public function reset_password()
	{
		$new_pw = Text::random('alnum', 10);
		
		$this->update_user(array('password' => $new_pw, 'password_confirm' => $new_pw), array('password'));
		
		return $new_pw;
			
	}
}