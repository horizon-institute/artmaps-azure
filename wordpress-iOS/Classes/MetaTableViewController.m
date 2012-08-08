//
//  MetaTableViewController.m
//  WordPress
//
//  Created by Shakir Ali on 26/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import "MetaTableViewController.h"
#import "EditPostViewController.h"
#import "ExperienceConfigurer.h"
#import "PostMapLocation.h"

@interface MetaTableViewController ()
-(void)showAddPostView;
-(void)postViewDismissed:(id)notification;
@end

@implementation MetaTableViewController
@synthesize ooiMeta;
@synthesize postMapLocation;

- (id)initWithStyle:(UITableViewStyle)style
{
    self = [super initWithStyle:style];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    [self setupNavigationBar];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(postViewDismissed:) name:@"PostEditorDismissed" object:nil];
}

-(void)setupNavigationBar{
    self.navigationItem.title = [[[ExperienceConfigurer sharedInstance] currentExperience] getTitleForOoI];
    [self setupRightBarButtonItem];
}

-(void)setupRightBarButtonItem{
    UIBarButtonItem *rightBarButtonItem = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemCompose target:self action:@selector(showAddPostView)];
    self.navigationItem.rightBarButtonItem = rightBarButtonItem;
    [rightBarButtonItem release];
}

- (void)viewDidUnload
{
    [super viewDidUnload];
    [[NSNotificationCenter defaultCenter] removeObserver:self];
    self.postMapLocation = nil;
    self.ooiMeta = nil;
}

-(void)dealloc{
    [[NSNotificationCenter defaultCenter] removeObserver:self];
    [postMapLocation release];
    [ooiMeta release];
    [super dealloc];
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

#pragma mark - Table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
    // Return the number of sections.
    return NO_OF_SECTIONS;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    // Return the number of rows in the section.
    return 1;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    UITableViewCell *cell = nil;
    switch (indexPath.section) {
        case kArtist:
            cell = [self cellForArtist:tableView];
            break;
        case kArtistDate:
            cell = [self cellForArtistDate:tableView];
            break;
        case kArtworkDate:
            cell = [self cellForArtworkDate:tableView];
            break;
        case kImageUrl:
            cell = [self cellForImage:tableView];
            break;
        case kReference:
            cell = [self cellForReference:tableView];
            break;
        case kTitle:
            cell = [self cellForTitle:tableView];
            break;
        default:
            break;
    }
    return cell;
}

-(UITableViewCell*)cellForArtist:(UITableView*)tableView{
    static NSString *ArtistCellIdentifier = @"ArtistCell";
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:ArtistCellIdentifier];
    if (cell == nil){
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue1 reuseIdentifier:ArtistCellIdentifier] autorelease];
    }
    cell.textLabel.text = ooiMeta.artist;    
    return cell;
}

-(UITableViewCell*)cellForArtistDate:(UITableView*)tableView{
    static NSString *ArtistDateCellIdentifier = @"ArtistDateCell";
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:ArtistDateCellIdentifier];
    if (cell == nil){
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue1 reuseIdentifier:ArtistDateCellIdentifier] autorelease];
    }
    cell.textLabel.text = ooiMeta.artistdate;    
    return cell;
}

-(UITableViewCell*)cellForArtworkDate:(UITableView*)tableView{
    static NSString *ArtworkDateCellIdentifier = @"ArtworkDateCell";
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:ArtworkDateCellIdentifier];
    if (cell == nil){
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue1 reuseIdentifier:ArtworkDateCellIdentifier] autorelease];
    }
    cell.textLabel.text = ooiMeta.artworkdate;    
    return cell;
}

-(UITableViewCell*)cellForReference:(UITableView*)tableView{
    static NSString *ReferenceCellIdentifier = @"ReferenceCell";
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:ReferenceCellIdentifier];
    if (cell == nil){
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue1 reuseIdentifier:ReferenceCellIdentifier] autorelease];
    }
    cell.textLabel.text = ooiMeta.reference;    
    return cell;
}

-(UITableViewCell*)cellForTitle:(UITableView*)tableView{
    static NSString *TitleCellIdentifier = @"TitleCell";
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:TitleCellIdentifier];
    if (cell == nil){
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue1 reuseIdentifier:TitleCellIdentifier] autorelease];
    }
    cell.textLabel.text = ooiMeta.title;    
    return cell;
}

-(UITableViewCell*)cellForImage:(UITableView*)tableView{
    static NSString *ImageCellIdentifier = @"ImageCell";
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:ImageCellIdentifier];
    if (cell == nil){
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue1 reuseIdentifier:ImageCellIdentifier] autorelease];
    }
    cell.textLabel.text = ooiMeta.imageurl;    
    return cell;
}

- (void)showAddPostView {
    
    Post *post = [Post newDraftForBlog:[ExperienceConfigurer sharedInstance].selectedBlog];
    EditPostViewController *editPostViewController = [[[EditPostViewController alloc] initWithPost:[post createRevision]] autorelease];
    editPostViewController.editMode = kNewPost;
    [editPostViewController refreshUIForCompose];
    editPostViewController.postMapLocation = self.postMapLocation;
    UINavigationController *navController = [[[UINavigationController alloc] initWithRootViewController:editPostViewController] autorelease];
    navController.modalPresentationStyle = UIModalPresentationPageSheet;
    [self.navigationController presentModalViewController:navController animated:YES];
    [post release];
}

-(void)postViewDismissed:(id)notification{
    [self.navigationController popViewControllerAnimated:YES];
}

@end
