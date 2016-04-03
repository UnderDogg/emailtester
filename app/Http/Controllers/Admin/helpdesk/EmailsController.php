<?php

namespace App\Http\Controllers\Admin\helpdesk;

// controllers
use App\Http\Controllers\Controller;
// request
use App\Http\Requests\helpdesk\EmailsEditRequest;
use App\Http\Requests\helpdesk\EmailsRequest;

//use App\Model\helpdesk\Agent\Department;
// model
use App\Model\helpdesk\Email\Emails;
//use App\Model\helpdesk\Manage\Help_topic;
use App\Model\helpdesk\Settings\Email;
//use App\Model\helpdesk\Ticket\Ticket_Priority;
use App\Model\helpdesk\Utility\MailboxProtocol;
use Crypt;
// classes
use Exception;
use Illuminate\Http\Request;

//use PhpImap\Mailbox as ImapMailbox;

/**
 * ======================================
 * EmailsController.
 * ======================================
 * This Controller is used to define below mentioned set of functions applied to the Emails in the system.
 *
 * @author Ladybird <info@ladybirdweb.com>
 */
class EmailsController extends Controller
{


  /**
   * Defining constructor variables.
   *
   * @return type
   */
  public function __construct()
  {
    $this->middleware('auth');
    $this->middleware('roles');
  }

  /**
   * Display a listing of the Emails.
   *
   * @param type Emails $emails
   *
   * @return type view
   */
  public function index(Emails $email)
  {
    try {
      // fetch all the emails from emails table
      $emails = $email->get();

      return view('themes.default1.admin.helpdesk.emails.emails.index', compact('emails'));
    } catch (Exception $e) {
      return redirect()->back()->with('fails', $e->getMessage());
    }
  }

  /**
   * Show the form for creating a new resource.
   *
   * @param type Department      $department
   * @param type Help_topic      $help
   * @param type Priority        $priority
   * @param type MailboxProtocol $mailbox_protocol
   *
   * @return type Response
   */
  public function create(MailboxProtocol $mailbox_protocol)
  {
    try {
      /*
      // fetch all the departments from the department table
      $departments = $department->get();
      // fetch all the helptopics from the helptopic table
      $helps = $help->get();
      // fetch all the types of priority from the ticket_priority table
      $priority = $ticket_priority->get();
      */
      // fetch all the types of mailbox protocols from the mailbox_protocols table
      $mailbox_protocols = $mailbox_protocol->get();
      // return with all the table data
      return view('themes.default1.admin.helpdesk.emails.emails.create', compact('mailbox_protocols'));
    } catch (Exception $e) {
      // return error messages if any
      return redirect()->back()->with('fails', $e->getMessage());
    }
  }

  /**
   * Check for email input validation.
   *
   * @param EmailsRequest $request
   *
   * @return int
   */
  public function validatingEmailSettings(Request $request)
  {
    /*
    $validator = \Validator::make(
                    [
                'email_address' => $request->email_address,
                'email_name'    => $request->email_name,
                'password'      => $request->password,
                    ], [
                'email_address' => 'required|email|unique:emails',
                'email_name'    => 'required',
                'password'      => 'required',
                    ]
    );
    */
    /*
    if ($validator->fails()) {
        $jsons = $validator->messages();
        $val = '';
        foreach ($jsons->all() as $key => $value) {
            $val .= $value;
        }
        $return_data = rtrim(str_replace('.', ',', $val), ',');

        return $return_data;
    }
    */


    if ($request->fetching_status == 'on') {
      $imap_check = $this->getImapStream($request);
      if ($imap_check == 0) {
        dd($imap_check);
        return 'Incoming email connection failed';
      }
      $need_to_check_imap = 1;
    } else {
      $imap_check = 0;
      $need_to_check_imap = 0;
    }

    //dd($need_to_check_imap);

    if ($request->sending_status == 'on') {
      $smtp_check = $this->getSmtp($request);
      if ($smtp_check == 0) {
        return 'Outgoing email connection failed';
      }
      $need_to_check_smtp = 1;
    } else {
      $smtp_check = 0;
      $need_to_check_smtp = 0;
    }


    dd($request);


    if ($request->fetching_protocol === "imap" || $request->fetching_protocol === "gmail") {
      if ($need_to_check_imap == 1 && $need_to_check_smtp == 1) {
        if ($imap_check != 0 && $smtp_check != 0) {
          $this->store($request);
          $return = 1;
        }
      } elseif ($need_to_check_imap == 1 && $need_to_check_smtp == 0) {
        if ($imap_check != 0 && $smtp_check == 0) {
          $this->store($request);
          $return = 1;
        }
      } elseif ($need_to_check_imap == 0 && $need_to_check_smtp == 1) {
        if ($imap_check == 0 && $smtp_check != 0) {
          $this->store($request);
          $return = 1;
        }
      } elseif ($need_to_check_imap == 0 && $need_to_check_smtp == 0) {
        if ($imap_check == 0 && $smtp_check == 0) {
          $this->store($request, null);
          $return = 1;
        }
      }
    } else {
      /*OK, we're dealing with exchange web services*/
      $this->store($request);

    }

    return $return;
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param type Emails        $email
   * @param type EmailsRequest $request
   *
   * @return type Redirect
   */
  public function store($request)
  {
    //        dd($request);
    $email = new Emails();
    try {
      //            getConnection($request->input('email_name'), $request->input('email_address'), $request->input('email_address'))
      // saving all the fields to the database
      if ($email->fill($request->except('password', 'department', 'priority', 'help_topic', 'fetching_status', 'sending_status'))->save() == true) {
        if ($request->fetching_status == 'on') {
          $email->fetching_status = 1;
        } else {
          $email->fetching_status = 0;
        }
        if ($request->sending_status == 'on') {
          $email->sending_status = 1;
        } else {
          $email->sending_status = 0;
        }

        // inserting the encrypted value of password
        $email->password = Crypt::encrypt($request->input('password'));
        $email->save(); // run save
        // Creating a default system email as the first email is inserted to the system
        $email_settings = Email::where('id', '=', '1')->first();
        $email_settings->sys_email = $email->id;
        $email_settings->save();
        // returns success message for successful email creation
//                return redirect('emails')->with('success', 'Email Created successfully');
        return 1;
      } else {
        // returns fail message for unsuccessful save execution
//                return redirect('emails')->with('fails', 'Email can not Create');
        return 0;
      }
    } catch (Exception $e) {
      // returns if try fails
//            return redirect()->back()->with('fails', $e->getMessage());
      return 0;
    }
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param type int             $id
   * @param type Department      $department
   * @param type Help_topic      $help
   * @param type Emails          $email
   * @param type Priority        $priority
   * @param type MailboxProtocol $mailbox_protocol
   *
   * @return type Response
   */
  public function edit($id, Emails $email, MailboxProtocol $mailbox_protocol)
  {
    try {
      // fetch the selected emails
      $emails = $email->whereId($id)->first();
      // get all the departments
      $departments = $department->get();
      // get all the helptopic
      $helps = $help->get();
      // get all the priority
      $priority = $ticket_priority->get();
      // get all the mailbox protocols
      $mailbox_protocols = $mailbox_protocol->get();
      // return if the execution is succeeded
      return view('themes.default1.admin.helpdesk.emails.emails.edit', compact('mailbox_protocols', 'priority', 'departments', 'helps', 'emails'));
    } catch (Exception $e) {
      // return if try fails
      return redirect()->back()->with('fails', $e->getMessage());
    }
  }

  /**
   * Check for email input validation.
   *
   * @param EmailsRequest $request
   *
   * @return int
   */
  public function validatingEmailSettingsUpdate($id, Request $request)
  {
    /*
    $validator = \Validator::make(
                    [
                'email_address' => $request->email_address,
                'email_name'    => $request->email_name,
                'password'      => $request->password,
                    ], [
                'email_address' => 'email',
                'email_name'    => 'required',
                'password'      => 'required',
                    ]
    );
    */
    /*
    if ($validator->fails()) {
        $jsons = $validator->messages();
        $val = '';
        foreach ($jsons->all() as $key => $value) {
            $val .= $value;
        }
        $return_data = rtrim(str_replace('.', ',', $val), ',');

        return $return_data;
    }
    */
//        return $request;


    if ($request->fetching_status == 'on') {
      $imap_check = $this->getImapStream($request);
      if ($imap_check == 0) {
        return 'Incoming email connection failed';
      }
      $need_to_check_imap = 1;
    } else {
      $imap_check = 0;
      $need_to_check_imap = 0;
    }


    //dd($request);


    if ($request->sending_status == 'on') {
      $smtp_check = $this->getSmtp($request);
      if ($smtp_check == 0) {
        return 'Outgoing email connection failed';
      }
      $need_to_check_smtp = 1;
    } else {
      $smtp_check = 0;
      $need_to_check_smtp = 0;
    }

    //dd($smtp_check);

    if ($need_to_check_imap == 1 && $need_to_check_smtp == 1) {
      if ($imap_check != 0 && $smtp_check != 0) {
        $this->update($id, $request);
        $return = 1;
      }
    } elseif ($need_to_check_imap == 1 && $need_to_check_smtp == 0) {
      if ($imap_check != 0 && $smtp_check == 0) {
        $this->update($id, $request);
        $return = 1;
      }
    } elseif ($need_to_check_imap == 0 && $need_to_check_smtp == 1) {
      if ($imap_check == 0 && $smtp_check != 0) {
        $this->update($id, $request);
        $return = 1;
      }
    } elseif ($need_to_check_imap == 0 && $need_to_check_smtp == 0) {
      if ($imap_check == 0 && $smtp_check == 0) {
        $this->update($id, $request);
        $return = 1;
      }
    }

    return $return;
  }

  /**
   * Update the specified resource in storage.
   *
   * @param type $id
   * @param type Emails            $email
   * @param type EmailsEditRequest $request
   *
   * @return type Response
   */
  public function update($id, $request)
  {
    try {
      // fetch the selected emails
      $emails = Emails::whereId($id)->first();
      // insert all the requested parameters with except
      $emails->fill($request->except('password', 'department', 'priority', 'help_topic', 'fetching_status', 'sending_status'))->save();
      if ($request->fetching_status == 'on') {
        $emails->fetching_status = 1;
      } else {
        $emails->fetching_status = 0;
      }
      if ($request->sending_status == 'on') {
        $emails->sending_status = 1;
      } else {
        $emails->sending_status = 0;
      }

      // inserting the encrypted value of password
      $emails->password = Crypt::encrypt($request->input('password'));
      $emails->save();
      // returns success message for successful email update
      $return = 1;
    } catch (Exception $e) {
      // returns if try fails
      $return = $e->getMessage();
    }

    return $return;
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param type int    $id
   * @param type Emails $email
   *
   * @return type Redirect
   */
  public function destroy($id, Emails $email)
  {
    // fetching the details on the basis of the $id passed to the function
    $default_system_email = Email::where('id', '=', '1')->first();
    if ($default_system_email->sys_email) {
      // checking if the default system email is the passed email
      if ($id == $default_system_email->sys_email) {
        return redirect('emails')->with('fails', 'You cannot delete system default Email');
      }
    }
    try {
      // fetching the database instance of the current email
      $emails = $email->whereId($id)->first();
      // checking if deleting the email is success or if it's carrying any dependencies
      if ($emails->delete() == true) {
        return redirect('emails')->with('success', 'Email Deleted sucessfully');
      } else {
        return redirect('emails')->with('fails', 'Email can not  Delete ');
      }
    } catch (Exception $e) {
      // returns if the try fails
      return redirect()->back()->with('fails', $e->getMessage());
    }
  }

  /**
   * Create imap connection.
   *
   * @param type $request
   *
   * @return type int
   */
  public function getImapStream($request)
  {
    $fetching_status = $request->input('fetching_status');
    $username = $request->input('email_address');
    $password = $request->input('password');
    $protocol_id = $request->input('mailbox_protocol');
    $fetching_protocol = '/' . $request->input('fetching_protocol');
    $fetching_encryption = '/' . $request->input('fetching_encryption');
    if ($fetching_encryption == 'none') {
      $fetching_encryption = 'novalidate-cert';
    }
    $mailbox_protocol = $fetching_protocol . $fetching_encryption;
    $host = $request->input('fetching_host');
    $port = $request->input('fetching_port');
    $mailbox = '{' . $host . ':' . $port . $mailbox_protocol . '}INBOX';
    try {
      $imap_stream = imap_open($mailbox, $username, $password);
    } catch (\Exception $ex) {
      return $ex->getMessage();
    }
    $imap_stream = imap_open($mailbox, $username, $password);
    if ($imap_stream) {
      $return = 1;
    } else {
      $return = 0;
    }

    return $return;
  }

  /**
   * Check connection.
   *
   * @param type $imap_stream
   *
   * @return type int
   */
  public function checkImapStream($imap_stream)
  {
    $check_imap_stream = imap_check($imap_stream);
    if ($check_imap_stream) {
      $imap_stream = 1;
    } else {
      $imap_stream = 0;
    }

    return $imap_stream;
  }

  /**
   * Get smtp connection.
   *
   * @param type $request
   *
   * @return int
   */
  public function getSmtp($request)
  {
    $sending_status = $request->input('sending_status');
    $mail = new \PHPMailer();
    //$mail->SMTPDebug = 2;                               // Enable verbose debug output
    $mail->isSMTP();
    $mail->Host = $request->input('sending_host');
    $mail->SMTPAuth = true;                               // Enable SMTP authentication

    //dd($request->input('user_name'));
    $mail->Username = $request->input('email_address');

    $mail->Password = $request->input('password');
    $mail->SMTPSecure = $request->input('sending_encryption');
    $mail->Port = $request->input('sending_port');

    //dd($mail->SMTPSecure);

    if ($mail->smtpConnect() == true) {
      $mail->smtpClose();
      $return = 1;
    } else {
      $return = 0;
    }

    //dd("basic PHPmailer SMTP failed");

    return $return;
  }

  /**
   * Checking if department value is null.
   *
   * @param type $dept
   *
   * @return type string or null
   */
  public function departmentValue($dept)
  {
    if ($dept) {
      $email_department = $dept;
    } else {
      $email_department = null;
    }

    return $email_department;
  }

  /**
   * Checking if priority value is null.
   *
   * @param type $priority
   *
   * @return type string or null
   */
  public function priorityValue($priority)
  {
    if ($priority) {
      $email_priority = $priority;
    } else {
      $email_priority = null;
    }

    return $email_priority;
  }

  /**
   * Checking if helptopic value is null.
   *
   * @param type $help_topic
   *
   * @return type string or null
   */
  public function helpTopicValue($help_topic)
  {
    if ($help_topic) {
      $email_help_topic = $help_topic;
    } else {
      $email_help_topic = null;
    }

    return $email_help_topic;
  }
}