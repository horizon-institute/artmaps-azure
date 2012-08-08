//
//  MetaListTableViewController.m
//  WordPress
//
//  Created by Shakir Ali on 04/08/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import "MetaListTableViewController.h"
#import "PostMapLocation.h"
#import "OoIMeta.h"
#import "OoIMetaLoader.h"
#import "MetaTableViewController.h"
#import "ExperienceConfigurer.h"

@interface MetaListTableViewController ()
@property (nonatomic, retain) NSMutableDictionary *metaLoadersInProgress;
@property (nonatomic, retain) NSMutableDictionary *ooiMetaDict;
@end

@implementation MetaListTableViewController

@synthesize ooI_IDs;
@synthesize postMapLocation;
@synthesize metaLoadersInProgress;
@synthesize ooiMetaDict;

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
    self.metaLoadersInProgress = [NSMutableDictionary dictionary];
    self.ooiMetaDict = [NSMutableDictionary dictionary];
    self.navigationItem.title = [[[ExperienceConfigurer sharedInstance] currentExperience] getTitleForOoI];
}

-(void)cancelMetaLoadRequests{
    NSArray *allMetaLoaders = [self.metaLoadersInProgress allValues];
    [allMetaLoaders makeObjectsPerformSelector:@selector(cancelMetaLoad)];
    [self.metaLoadersInProgress removeAllObjects];
}

- (void)viewDidUnload
{
    [super viewDidUnload];
    [self cancelMetaLoadRequests];
    self.metaLoadersInProgress = nil;
    self.ooI_IDs = nil;
    self.postMapLocation = nil;
    self.ooiMetaDict = nil;
}

#pragma mark - memory management

-(void)dealloc{
    NSArray *allMetaLoaders = [self.metaLoadersInProgress allValues];
    [allMetaLoaders makeObjectsPerformSelector:@selector(cancelMetaLoad)];
    [metaLoadersInProgress release];
    [ooI_IDs release];
    [postMapLocation release];
    [ooiMetaDict release];
    [super dealloc];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // terminate all pending download connections
    [self cancelMetaLoadRequests];
}


- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

#pragma mark - Table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
   // Return the number of sections.
   return 1;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    // Return the number of rows in the section.
    return [ooI_IDs count];
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    static NSString *CellIdentifier = @"Cell";
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    // Configure the cell...
    if (cell == nil){
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleSubtitle reuseIdentifier:CellIdentifier] autorelease];
        cell.accessoryType = UITableViewCellAccessoryDisclosureIndicator; 
    }
    
    OoIMeta *meta = [self.ooiMetaDict objectForKey:indexPath];
    if (meta != nil){
        [self displayOoIMeta:meta inTableViewCell:cell];
    }else{
        cell.textLabel.text = NSLocalizedString(@"Loading...", nil);
        cell.detailTextLabel.text = @"";
        [self submitMetaLoaderRequestForIndexPath:indexPath];
    }
    return cell;
}

-(void)submitMetaLoaderRequestForIndexPath:(NSIndexPath*)indexPath{
    OoIMetaLoader *metaLoader = [metaLoadersInProgress objectForKey:indexPath];
    if (metaLoader == nil){
        OoIMetaLoader *metaLoader = [[OoIMetaLoader alloc] init];
        metaLoader.delegate = self;
        [metaLoader submitOoIMetaRequestWithID:[ooI_IDs objectAtIndex:indexPath.row] forIndexPathInTableView:indexPath];
        [metaLoadersInProgress setObject:metaLoader forKey:indexPath];
        [metaLoader release];
    }
}

-(void)displayOoIMeta:(OoIMeta*)meta inTableViewCell:(UITableViewCell*)cell{
    cell.textLabel.text = meta.title;
    cell.detailTextLabel.text = meta.artist;
}


#pragma mark - Table view delegate

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    OoIMeta *meta = [ooiMetaDict objectForKey:indexPath];
    if (meta != nil){
        MetaTableViewController *metaTableViewController = [[MetaTableViewController alloc] initWithNibName:@"MetaTableViewController" bundle:nil];
        metaTableViewController.ooiMeta = meta;
        metaTableViewController.postMapLocation = postMapLocation;
        [self.navigationController pushViewController:metaTableViewController animated:YES];
        [metaTableViewController release];
    }
}

#pragma mark - OoIMetaLoaderDelegate
-(void)metaDataDidLoad:(OoIMeta *)ooiMeta forIndexPath:(NSIndexPath *)indexPath{
    OoIMetaLoader *metaLoader = [metaLoadersInProgress objectForKey:indexPath];
    if (metaLoader != nil){
        [ooiMetaDict setObject:ooiMeta forKey:indexPath];
        [self.tableView reloadRowsAtIndexPaths:[NSArray arrayWithObject:indexPath] withRowAnimation:UITableViewRowAnimationNone];
        metaLoader.delegate = nil;
    }
}

@end
